<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use mysqli;
use stdClass;

class CityController extends Controller
{
    public function getPaginatedCities()
    {
        // $cities = [
        //     (object)[
        //         'name' => 'Porto',
        //         'country' => 'PRT',
        //         'pop' => '100'
        //     ],
        //     (object)[
        //         'name' => 'Lisboa',
        //         'country' => 'PRT',
        //         'pop' => '200'
        //     ],
        //     (object)[
        //         'name' => 'Paris',
        //         'country' => 'FRA',
        //         'pop' => '500'
        //     ],
        //     (object)[
        //         'name' => 'London',
        //         'country' => 'GBR',
        //         'pop' => '800'
        //     ]
        // ];

        $result = DB::select('SELECT COUNT(*) AS cityTotal FROM city');
        $row = $result[0];
        $cityTotal = $row->cityTotal;

        $num_rows = isset($_GET['num_rows']) ? $_GET['num_rows'] : 20;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        $lastPage = ceil($cityTotal / $num_rows);


        if (!is_numeric($page) or $page < 1 or $page > $lastPage) {
            return view("invalidPage");
        }

        $offset = ($page - 1) * $num_rows;

        $bindings = [$num_rows, $offset];
        $cities = DB::select("SELECT * FROM city LIMIT ? OFFSET ?", $bindings);

        $data = [
            'cities' => $cities,
            'page' => $page,
            'lastPage' => $lastPage,
            'num_rows' => $num_rows
        ];

        return view("paginatedCities", $data);
    }

    public function getAddCity()
    {
        $success = isset($_GET["success"]);
        $fail = isset($_GET["fail"]);

        $countries = DB::select("SELECT Code, Name FROM country");

        $data = [
            "success" => $success,
            "fail" => $fail,
            "countries" => $countries
        ];

        return view("addCity", $data);
    }

    public function postAddCity()
    {
        $name = isset($_POST["name"]) ? $_POST["name"] : "";
        $countryCode = isset($_POST["countryCode"]) ? $_POST["countryCode"] : "PRT";
        $district = isset($_POST["district"]) ? $_POST["district"] : "";
        $population = isset($_POST["population"]) ? $_POST["population"] : 0;

        // Inserir na BD;
        $bindings = [$name, $countryCode, $district, $population];
        $success = DB::insert("INSERT INTO city VALUES (NULL, ?, ?, ?, ?)", $bindings);

        // Feito
        if ($success) {
            return redirect("/addCity?success");
        } else {
            return redirect("/addCity?fail");
        }
    }
}
