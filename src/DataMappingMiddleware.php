<?php

namespace Agilize\LaravelDataMapper;

use Agilize\LaravelDataMapper\Helpers\StringHelper;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

class DataMappingMiddleware
{
    protected const DEFAULT_API_PREFIX = 'api/';
    public const WITH_RELATIONS = 'with-relations';
    public const NO_RELATIONS = 'no-relations';

    protected $params = [];
    protected $withRelations = true;
    protected $config;


    /**
     * DataMappingMiddleware constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request  $request
     * @param \Closure $next
     * @param string   $withRelations
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $withRelations = self::WITH_RELATIONS)
    {
        if (!is_string($withRelations) || $withRelations === self::NO_RELATIONS) {
            $this->withRelations = false;
        }

        $this->setParams($request);

        try {
            $request->request = $this->mapObjectFromParams($request->request);
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }

        return $next($request);
    }

    /**
     * @param $request
     */
    private function setParams($request)
    {
        $fullUrl = $request->fullUrl();
        $parsedUrl = parse_url($fullUrl);

        $this->addPathParams($parsedUrl);
        $this->addQueryParams($parsedUrl);
        $this->addBagParams($request->request);
    }

    /**
     * @param array $parsedUrl
     */
    private function addPathParams($parsedUrl)
    {
        $path = $this->cleanUrlPath($parsedUrl['path']);

        $pathPieces = explode('/', $path);

        $iterator = new \ArrayIterator($pathPieces);
        while ($iterator->valid()) {
            $current = $iterator->current();
            $iterator->next();
            if (!$iterator->valid()) {
                break;
            }
            $this->params[$current] = $iterator->current();
            $iterator->next();
        }
    }

    /**
     * @param array $parsedUrl
     */
    private function addQueryParams($parsedUrl)
    {
        if (array_key_exists('query', $parsedUrl)) {
            parse_str($parsedUrl['query'], $queries);
            $this->params = array_merge($this->params, $queries);
        }
    }

    /**
     * @param ParameterBag $bag
     */
    private function addBagParams(ParameterBag $bag)
    {
        if ($bag->count() > 0) {
            $this->params = array_merge($this->params, $bag->all());
        }
    }

    /**
     * @return array
     */
    private function getFileListFromPackages()
    {
        $fileList = [];
        $entityDirectoryPath = __DIR__ . '/' . $this->config['entityDirectory'];
        $recursiveIterator = new \RecursiveDirectoryIterator($entityDirectoryPath);
        foreach (new \RecursiveIteratorIterator($recursiveIterator) as $file) {
            $splitPath = explode('/', $file);
            $fileName = end($splitPath);
            if ($fileName != '.' && $fileName != '..') {
                $fileList[$fileName] = $file;
            }
        }
        return $fileList;
    }

    /**
     * @param  ParameterBag $bag
     * @return ParameterBag
     * @throws \Exception
     */
    private function mapObjectFromParams(ParameterBag $bag)
    {
        $result = null;
        $fileList = $this->getFileListFromPackages();
        foreach ($this->params as $term => $value) {
            $domainName = StringHelper::transformWithHyphenOrUnderScoreToCapitalized($term);
            $fileName = $domainName . '.php';
            if (array_key_exists($fileName, $fileList)) {
                $object = $this->getObjectInstanceFromFileAndDomain($fileList[$fileName], $domainName);
                if (is_object($object) && $this->validatePrimaryKeyType($value)) {
                    if ($this->withRelations) {
                        $result = $object->find($value);
                        if ($result instanceof Model) {
                            if (method_exists($object, 'scopeWithAll')) {
                                $result = $result->withAll();
                            }
                            $result = $result->first();
                        }
                    }

                    if (!$this->withRelations) {
                        $result = $object->find($value);
                    }
                }
            }
            if ($result != null) {
                $value = $result;
            }
            $bag->set($term, $value);
        }

        return $bag;
    }

    /**
     * @param  \SplFileInfo $splFileInfo
     * @param  $domainName
     * @return mixed
     *
     * @throws \Exception
     */
    private function getObjectInstanceFromFileAndDomain(\SplFileInfo $splFileInfo, $domainName)
    {
        $content = file_get_contents($splFileInfo->getRealPath());
        $namespace = $this->getCleanNamespaceFromClassContent($content);
        if (!empty($namespace)) {
            if (class_exists($class = $namespace . '\\' . $domainName)) {
                return new $class();
            }
        }
        return null;
    }

    /**
     * @param  $content
     * @return mixed|string
     * @throws \Exception
     */
    private function getCleanNamespaceFromClassContent($content)
    {
        $pattern = '/(namespace+.*)/';
        if (preg_match($pattern, $content, $matches)) {
            $namespace = rtrim($matches[0], ';');
            $splitNamespace = explode(' ', $namespace);
            return end($splitNamespace);
        }
        return null;
    }

    /**
     * @param $parsedUrl
     *
     * @return string|string[]
     */
    private function cleanUrlPath($parsedUrl)
    {
        $path = trim($parsedUrl, '/');
        if (strpos($path, self::DEFAULT_API_PREFIX) !== false) {
            $path = str_replace(self::DEFAULT_API_PREFIX, '', $path);
        }
        if (strpos($path, $this->config['apiVersion']) !== false) {
            $path = str_replace($this->config['apiVersion'], '', $path);
        }
        return $path;
    }

    /**
     * @param  $value
     * @return bool
     */
    private function validatePrimaryKeyType($value)
    {
        if ($this->config['primaryKeyType'] == 'uuid') {
            return StringHelper::isValidUuid($value);
        }
        return StringHelper::isValidNumericId($value);
    }
}
