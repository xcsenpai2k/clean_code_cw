<?php 

namespace App\Repositories\Eloquent;

use App\Models\Country;
use App\Repositories\CountryRepositoryInterface;

class CountryRepository extends BaseRepository implements CountryRepositoryInterface 
{
    public function __construct(Country $country)
    {
        $this->model = $country;
    }
}