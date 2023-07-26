<?php
    namespace App\Http\Controllers\Admin;

    use App\Http\Controllers\Controller;
    use App\Models\Package;
    use Illuminate\Http\Request;
    use Illuminate\Support\Arr;
    use Illuminate\Support\Facades\File;
    use Illuminate\Support\Facades\Gate;
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\View;
    use Illuminate\Support\Str;
    use League\Flysystem\Filesystem;
    use Illuminate\Support\Facades\Process;
    use Waavi\Translation\Models\Translation;
    use Illuminate\Support\Facades\Validator;

    class TranslateController extends Controller {

        public function __construct() {
            if(!request()->route()) return;
            $this->routeNamespace = Str::before(request()->route()->getName(), '.translations');
            View::composer('admin/translations*', function($view) {
                $view->with([
                    'route_namespace' => $this->routeNamespace,
                ]);
            });

            // @HOOK_CONSTRUCT
        }

        private function searchInPath($search, $path) {
            if(!realpath($path)) return [];
            $command = Package::replaceEnvCommand("grep --include=\*.php -rnw -e '{$search}'");

            $process = Process::forever()->path($path)->run( $command );
            // executes after the command finishes
            $return = [];
            if ($process->successful()) {//found in files
                $results = explode("\n", $process->output());
                foreach($results as $index => $resultRow) {
                    if(!$resultRow) {
                        unset($results[$index]); continue;
                    };
                    $resultRow = explode(':', $resultRow);
                    $return[] = str_replace('.php', '', $resultRow[0]);
                }
                return $return;
            }
//                return false;
//            throw new \Illuminate\Process\Exceptions\ProcessFailedException($process);
            return $return;
        }

        private function search($search, $chNamespace, $locale = null) {
            $locale = $locale?? app()->getLocale();
            $namespaces = $this->getNamespaces();
            $groups = $this->searchInPath($search, $namespaces[$chNamespace].DIRECTORY_SEPARATOR.$locale);
            $dbTranslations = \Waavi\Translation\Models\Translation::where([
                'locale' => $locale,
                'namespace' => $chNamespace,
            ])->where(function($qry) use ($search) {
               $qry->where('item', 'LIKE', "%{$search}%")
                    ->orWhere('text', 'LIKE', "%{$search}%");
            })->get();
            foreach($dbTranslations as $dbTranslation) {
                $groups[] = $dbTranslation->group;
            }
            if($locale != config('app.fallback_locale')) {
                $groups = array_merge( $groups, $this->search($search, $chNamespace, config('app.fallback_locale')) );
            }
            return array_unique($groups);
        }

        private function getNamespaces() {
            return array_merge([
                '*' => lang_path()
            ], app('translator')->getLoader()->namespaces());;
        }

        private static function getDirs($directory, $replacepath = false) {
            $return = [];
            $replacepath = $replacepath? $replacepath : $directory;
            foreach(glob($directory.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR) as $path) {
                $return[] = str_replace($replacepath.DIRECTORY_SEPARATOR, '', $path);
                $return = array_merge($return, static::getDirs($path, $replacepath));
            }
            return $return;
        }

        private static function getGroups($localeFolder, $recursive = true, $allowedGroups = null) {
            $groups = $recursive?
                static::langReGlob( $localeFolder, '*.php' ) :
                glob($localeFolder.DIRECTORY_SEPARATOR.'*.php');
            if(is_array($allowedGroups)) $allowedGroups = array_flip($allowedGroups);
            $return = [];
            foreach($groups as $langFile) {
                $langFile = str_replace($localeFolder.DIRECTORY_SEPARATOR, '',
                    substr($langFile, 0, -1*strlen('.php')));
//                dump($langFile);
                if(is_array($allowedGroups) && !isset($allowedGroups[$langFile])) continue;
                $return[] =  $langFile;
            }
            return $return;
        }

        private static function langReGlob($directory, $pattern) {
            $return = glob($directory.DIRECTORY_SEPARATOR.$pattern);
            foreach(glob($directory.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR) as $path) {
                $return = array_merge(static::langReGlob($path, $pattern), $return);
            }
            return $return;
        }

        public function index() {

            $namespaces = $this->getNamespaces();

            $chNamespace = '*';
            if($filters = request()->get('filters')) {
                if(isset($filters['namespace']) && isset($namespaces[ $filters['namespace'] ])) {
                    $chNamespace = $filters['namespace'];
                }
            }
            $namespacePath = $namespaces[$chNamespace].DIRECTORY_SEPARATOR.config('app.fallback_locale');

            $search = $allowedGroups = null;
            if(request()->has('search')) {
                $search = request()->get('search');
                if(is_null($search)) {
                    $routeQry = request()->query();
                    unset($routeQry['search']);
                    return redirect()->route(Route::currentRouteName(), array_merge(
                        Route::current()->parameters(),
                        $routeQry
                    )) ;
                }
                $allowedGroups = $this->search($search, $chNamespace);
            }

            $dirs = static::getDirs($namespacePath);
            $chDir = 'all';
            if(isset($filters['dir'])) {
                if(in_array($filters['dir'], $dirs)) {
                    $chDir = $filters['dir'];
                    $namespacePath .= DIRECTORY_SEPARATOR.$chDir;
                    if(is_array($allowedGroups)) {
                        foreach($allowedGroups as $index => $allowedGroup) {
                            $allowedGroups[$index] = Str::after($allowedGroup, $chDir.DIRECTORY_SEPARATOR);
                        }
                    }
                } elseif($filters['dir'] == 'in') {
                    $chDir = $filters['dir'];
                }
            }
//            dump($allowedGroups);
            $groups = $this->getGroups($namespacePath, ($chDir != 'in'), $allowedGroups);
//            dd($groups);

            $chGroup = 'all';
            if(isset($filters['group']) && in_array($filters['group'], $groups)) {
                $chGroup = $filters['group'];
            }

            if(request()->has('show_page')) {
                return view('admin/translations/page', [
                    'namespaces' => $namespaces,
                    'dirs' => $dirs,
                    'groups' => $groups,
                    'chNamespace' => $chNamespace,
                    'chGroup' => $chGroup,
                    'chDir' => $chDir,
                    'search' => $search,
                ]);
            }
            if(request()->has('show_translations')) {
                if($chGroup == 'all') abort(422);
                $langFile = $namespacePath.DIRECTORY_SEPARATOR.$chGroup.'.php';
                if(!is_file($langFile)) abort(422);
                $translations = include( $langFile );
                if(!is_array($translations)) abort(422);
                $translations = \Illuminate\Support\Arr::dot($translations);
                $dirGroup = in_array($chDir, ['all', 'in'])? $chGroup : $chDir.'/'.$chGroup;
                return view('admin/translations/values', [
                    'chNamespace' => $chNamespace,
                    'chDir' => $chDir,
                    'chGroup' => $chGroup,
                    'namespacePath' => $namespacePath,
                    'dirGroup' => $dirGroup,
                    'translations' => $translations,
                    'search' => $search,
                ]);
            }

            return view('admin/translations/translations', compact(
                'namespaces', 'chNamespace','dirs', 'namespacePath',
                'chDir', 'groups', 'chGroup' ));
        }

//        public function values(Request $request) {
//            $namespaces = $this->getNamespaces();
//            $chNamespace = $chDir = $chGroup = null;
//            $validatedData = Validator::make(request()->all(), [
//                'namespace' => ['required', 'max:255', function($attribute, $value, $fail) use ($namespaces, &$chNamespace) {
//                    if(!isset($namespaces[$value])) {
//                        return $fail( trans('admin/translations/validation.namespace.required'));
//                    }
//                    $chNamespace = $value;
//                }],
//                'dir' => ['required', 'max:255', function($attribute, $value, $fail) use ($namespaces, $chNamespace, &$chDir){
//                    if(!$chNamespace) return $fail( trans('admin/translations/validation.dir.required'));
//                    $namespacePath = $namespaces[$chNamespace].DIRECTORY_SEPARATOR.config('app.fallback_locale');
//                    $dirs = static::getDirs($namespacePath);
//                    if(!in_array($value, $dirs)) {
//                        return $fail( trans('admin/translations/validation.dir.required'));;
//                    }
//                    $chDir = $value;
//                }],
//                'group' => ['required', 'max:255', function($attribute, $value, $fail) use ($namespaces, $chNamespace, $chDir, &$chGroup) {
//                    if(!$chNamespace || !$chDir ) return $fail( trans('admin/translations/validation.group.required'));
//                    $groupsPath = implode(DIRECTORY_SEPARATOR, [
//                        $namespaces[$chNamespace],
//                        config('app.fallback_locale'),
//                        $chDir
//                    ]);
//                    $groups = static::getGroups($groupsPath, false);
//                    if(!in_array($value, $groups)) {
//                        return $fail( trans('admin/translations/validation.group.required'));
//                    }
//                    $chGroup = $value;
//                }],
//            ], Arr::dot((array)trans('admin/translations/validation')))->validate();
//
//            return view('admin/translations/translations', [
//                'translations' => $translations,
//            ];
//        }

        public function store() {

        }

        public function update(Request $request) {
            $namespaces = $this->getNamespaces();
            if(!$request->has('namespace') || !isset($namespaces[$request->input('namespace')])) {
                return response()->json([
                    'error' => trans('admin/translations/validation.namespace.require')
                ], 400);
            }
            $chNamespace = $request->input('namespace');
            $namespacePath = $namespaces[$chNamespace].DIRECTORY_SEPARATOR.config('app.fallback_locale');
            $dirs = $this->getDirs($namespacePath);
            if(!$request->has('dir') || (!in_array( ($chDir = $request->input('dir')), ['all', 'in']) && !in_array($chDir, $dirs))) {
                return response()->json([
                    'error' => trans('admin/translations/validation.dir.require')
                ], 400);
            }
            if(!in_array($chDir, ['all', 'in'])) {
                $namespacePath .= DIRECTORY_SEPARATOR.$chDir;
            }
            $groups = $this->getGroups($namespacePath, ($chDir != 'in'));
            if(!$request->has('group') || !in_array(($chGroup = $request->input('group')), $groups)) {
                return response()->json([
                    'error' => trans('admin/translations/validation.group.require')
                ], 400);
            }
            Translation::updateOrCreate(array_merge([
                'locale' => app()->getLocale()
            ], [
                'namespace' => $chNamespace,
                'group' => (in_array($chDir, ['all', 'in'])? $chGroup : $chDir.'/'.$chGroup),
                'item' => $request->input('item')
            ]),[
                'text' => (string)request()->input('text')
            ]);

            return response()->json([
                'success' => true
            ]);
        }

        public function destroy() {

        }
    }
