<?php


namespace MyCollegeAPI\Models;
use Illuminate\Database\Eloquent\Model;


class NutritionalInformation extends Model {
    //table name
    protected $table = 'NutritionalInformation';
    //primary key
    protected $primaryKey = 'NutritionalInformationID';
    //PK is numeric
    public $incrementing = true;

    public $timestamps = false;

    //Columns
    public $nutritionalInformationID;

    public $servingSize;

    public $calories;
    
    public $totalFat;
    
    public $sodium;

    public $cholesterol;

    public $carbohydrates;

    public $sugars;

    public $protein;
    
    public $vitaminA;

    public $vitaminC;

    public $calcium;

    public $iron;
    
    public $itemID;

    public static function getNutritionalInformationById(string $ID) {
        $nutritionalinformationitem = self::findOrFail($ID);
        return $nutritionalinformationitem;
    }

    //View all data from table.
    public static function getData(){
        //$jsonData = self::all();
        //return $jsonData;
        /*********** code for pagination and sorting *************************/
        //get the total number of row count
        $count = self::count();

        //Get querystring variables from url
        $params = $request->getQueryParams();

        //do limit and offset exist?
        $limit = array_key_exists('limit', $params) ? (int)$params['limit'] : 10;   //items per page
        $offset = array_key_exists('offset', $params) ? (int)$params['offset'] : 0;  //offset of the first item

        //pagination
        $links = self::getLinks($request, $limit, $offset);

        //build query
        $query = self::with('classes');  //build the query to get all courses
        $query = $query->skip($offset)->take($limit);  //limit the rows

        //code for sorting
        $sort_key_array = self::getSortKeys($request);
        //soft the output by one or more columns
        foreach ($sort_key_array as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        //retrieve the courses
        $courses = $query->get();  //Finally, run the query and get the results

        //construct the data for response
        $results = [
            'totalCount' => $count,
            'limit' => $limit,
            'offset' => $offset,
            'links' => $links,
            'sort' => $sort_key_array,
            'data' => $courses
        ];

        return $results;
    }
    // Return an array of links for pagination. The array includes links for the current, first, next, and last pages.
    private static function getLinks($request, $limit, $offset) {
        $count = self::count();

        // Get request uri and parts
        $uri = $request->getUri();
        if($port = $uri->getPort()) {
            $port = ':' . $port;
        }
        $base_url = $uri->getScheme() . "://" . $uri->getHost() . $port . $uri->getPath();

        // Construct links for pagination
        $links = [];
        $links[] = ['rel' => 'self', 'href' => "$base_url?limit=$limit&offset=$offset"];
        $links[] = ['rel' => 'first', 'href' => "$base_url?limit=$limit&offset=0"];
        if ($offset - $limit >= 0) {
            $links[] = ['rel' => 'prev', 'href' => "$base_url?limit=$limit&offset=" . $offset - $limit];
        }
        if ($offset + $limit < $count) {
            $links[] = ['rel' => 'next', 'href' => "$base_url?limit=$limit&offset=" . $offset + $limit];
        }
        $links[] = ['rel' => 'last', 'href' => "$base_url?limit=$limit&offset=" . $limit * (ceil($count / $limit) - 1)];

        return $links;
    }
    private static function getSortKeys($request) {
        $sort_key_array = [];

        // Get querystring variables from url
        $params = $request->getQueryParams();

        if (array_key_exists('sort', $params)) {
            $sort = preg_replace('/^\[|\]$|\s+/', '', $params['sort']);  // remove white spaces, [, and ]
            $sort_keys = explode(',', $sort); //get all the key:direction pairs
            foreach ($sort_keys as $sort_key) {
                $direction = 'asc';
                $column = $sort_key;
                if (strpos($sort_key, ':')) {
                    list($column, $direction) = explode(':', $sort_key);
                }
                $sort_key_array[$column] = $direction;
            }
        }

        return $sort_key_array;
    }


    // public function setData($nutritionalInformationID, $servingSize, $calories, $totalFat, $sodium, $cholesterol, $carbohydrates, $sugars, $protein, $vitaminA, $vitaminC, $calcium, $iron, $itemID){
    //     $this->nutritionalInformationID = $nutritionalInformationID;
    //     $this->servingSize = $servingSize;
    //     $this->calories = $calories;
    //     $this->totalFat = $totalFat;
    //     $this->sodium = $sodium;
    //     $this->cholesterol = $cholesterol;
    //     $this->carbohydrates = $carbohydrates;
    //     $this->sugars = $sugars;
    //     $this->protein = $protein;
    //     $this->vitaminA = $vitaminA;
    //     $this->vitaminC = $vitaminC;
    //     $this->calcium = $calcium;
    //     $this->iron = $iron;
    //     $this->itemID = $itemID;
    // }
}