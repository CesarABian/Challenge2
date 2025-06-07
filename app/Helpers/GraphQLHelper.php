<?php

namespace App\Helpers;

use ArrayObject;
use GraphQL\Type\Schema;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Queries\AbstractQuery;
use App\Models\Product;
use App\Models\ProductVariant;
use GraphQL\Type\Definition\ObjectType;
use Illuminate\Support\Facades\Storage;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\FieldDefinition;
use GraphQL\Language\AST\OperationDefinitionNode;
use Illuminate\Pagination\LengthAwarePaginator;
use Rebing\GraphQL\Support\PaginationType;

class GraphQLHelper
{
    public static function createResolveInfo(): ResolveInfo
    {
        $config = [
            'name' => 'any',
            'resolve' => null,
            'args' => null,
            'argsMapper' => null,
            'description' => null,
            'visible' => null,
            'deprecationReason' => null,
            'astNode' => null,
            'complexity' => null,
            'type' => new class extends Type {
                public function toString(): string
                {
                    return '';
                }
            },
        ];
        return new ResolveInfo(
            new FieldDefinition($config),
            new ArrayObject,
            new ObjectType($config),
            [],
            new Schema([]),
            [],
            null,
            new OperationDefinitionNode([]),
            []
        );
    }

    public static function resolveData(mixed $data, mixed $query, ResolveInfo $info)
    {
        if (!$data || !key_exists('args', $data)) return;

        return $query->resolve(null, $data['args'] ?? [], null, $info, function () use ($data) {
            return new class($data) {
                public $select = '';

                public function __construct($data)
                {
                    $this->select = $data['select'];
                }

                public function getSelect()
                {
                    return $this->select;
                }
            };
        });
    }

    public static function resetCache(AbstractQuery $query)
    {
        $folder = $query->getAttributes()['name'];
        $files = Storage::allFiles("database/{$folder}");
        $info = self::createResolveInfo();
        foreach ($files as $file) {
            $cacheKey = basename($file);
            $path = "database/{$folder}/{$cacheKey}";
            $data = null;
            if (Storage::exists($path)) {
                $data = Storage::get($path);
                $data = json_decode($data, true);
            }
            Storage::delete($file);
            self::resolveData($data, $query, $info);
        }
    }

    public static function getQueryObject(mixed $query)
    {
        $queries = config('graphql.schemas.default.query');
        $queries = $queries[$query];
        return new $queries;
    }

    public static function validateCodeDuplicates(mixed $code, mixed $oldCode = null)
    {
        if ($code == $oldCode) return;
        $existsInProducts = Product::where('code', $code)->exists();
        $existsInVariants = ProductVariant::where('code', $code)->exists();

        if ($existsInProducts || $existsInVariants) {
            throw new \Exception();
        }
    }
}
