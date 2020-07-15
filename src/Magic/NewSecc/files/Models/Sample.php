<?php
namespace App\Models;

use angelrove\membrillo\Eloquent\ModelPlus;
use Illuminate\Database\Eloquent\SoftDeletes;
// use App\Models\Eloquent\Scopes\CompanyScope;

class [name_model] extends ModelPlus
{
    use SoftDeletes;

    protected $table = "[name_table]";

    public static $profiles = [
       'admin' => 'Admin',
       'basic' => 'Basic',
    ];

    // protected $fillable = [
    // ];

    protected $guarded = [
    ];

    //-------------------------------------------------------
    // Relationships
    //-------------------------------------------------------
    public function department()
    {
        // return $this->belongsTo('App\Models\Eloquent\UserDepartment');
    }

    //-------------------------------------------------------
    // Global Scopes
    //-------------------------------------------------------
    protected static function boot()
    {
        parent::boot();

        // static::addGlobalScope(new CompanyScope);
    }

    //-------------------------------------------------------
    // Scopes
    //-------------------------------------------------------
    public function scopeSearch($query, Request $request)
    {
        $inputs = $request->only(['name']);

        foreach ($inputs as $key => $value) {
            $query->where($key, 'LIKE', '%' . $value . '%');
        }

        return $query;
    }
}
