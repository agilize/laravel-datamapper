<?php

namespace Agilize\LaravelDataMapper;

use Agilize\LaravelDataMapper\Service\DataMappingService;
use Closure;
use Illuminate\Http\Request;

class DataMappingMiddleware
{
    public const WITH_RELATIONS = 'with-relations';
    public const NO_RELATIONS = 'no-relations';

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
     * @param Request $request
     * @param \Closure $next
     * @param string $relationship
     * @return mixed
     */
    public function handle($request, Closure $next, $relationship = self::WITH_RELATIONS)
    {
        $withRelations = true;
        if (!is_string($relationship) || $relationship === self::NO_RELATIONS) {
            $withRelations = false;
        }

        try {
            $dataMappingService = new DataMappingService();
            $request = $dataMappingService->handleDataMapping($request, $this->config, $withRelations);
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }

        return $next($request);
    }
}
