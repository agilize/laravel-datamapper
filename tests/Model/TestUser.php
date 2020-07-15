<?php

namespace Agilize\LaravelDataMapper\Tests\Model;

use Illuminate\Database\Eloquent\Model;

class TestUser extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAll()
    {
        return $this->with('testUserRole');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function testUserRole()
    {
        return $this->hasMany(TestUserRole::class);
    }

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }
}
