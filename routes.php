<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use GuzzleHttp\Client;

return function (App $app) {

 
    $container = $app->getContainer();
   
    /**
     * Route to request upcoming movies
     */

    $app->get('/mostpopular/[{page}]', function ($request, $response, $args) {


        /**
         * Request Genre list names
         */

        $client2 = new GuzzleHttp\Client();

        $genre = $client2->get('https://api.themoviedb.org/3/genre/movie/list?api_key=1b5adf76a72a13bad99b8fc0c68cb085&language=en-US');

        $dataGenreNames = json_decode($genre->getBody()->getContents());
      
        $client = new GuzzleHttp\Client();
        

        #----------end request genre----------------------#

        /**
         * Request mostpopular
         */

        $responssse="";
        $data="";
        $page ="";
        $page = $args['page'] ;

        $responssse = $client->get('https://api.themoviedb.org/3/movie/upcoming?api_key=1b5adf76a72a13bad99b8fc0c68cb085&language=en-US&page='.$args['page']);
        
        $data = json_decode($responssse->getBody()->getContents());

        $genresmovie = $data->results;
     
        $total_pages =$data->total_pages;
         #----------end request most popular----------------------#



        foreach ($genresmovie as $key => $value0) {
            
          $genre=  findNamesGenre($value0->genre_ids,$dataGenreNames);
          
            if($value0->poster_path!=""){

                $imgscr = "https://image.tmdb.org/t/p/w185".$value0->poster_path;

            }else{
                $imgscr ='https://t4.ftcdn.net/jpg/00/27/87/49/240_F_27874901_o1OlVbzf5RXayuWccfEsGsWZoEcxTdT7.jpg';
            }

            $newDate = date("m-d-Y", strtotime($value0->release_date));

            $arrayData['results'][] = array(
                'id'=>$value0->id,
                'title'=>$value0->title,
                'overview'=>$value0->overview,
                'poster_path'=>$imgscr, 
                'release_date'=>$newDate, 
                'genre'=>$genre,
                'total_pages'=>$total_pages 
                 );

        }

        return $response->withJson($arrayData)
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
        #--------------------------------------------------------------#
        
    });


    /**
     * Route to request search movies
     * @param{string or int}  term
     */

    $app->get('/moviesearch/[{term}]', function ($request, $response, $args) {
      
        $client = new GuzzleHttp\Client();

        $resp="";
        $data_m="";

        $resp = $client->get('https://api.themoviedb.org/3/search/movie?api_key=1b5adf76a72a13bad99b8fc0c68cb085&query='.$args['term']);
        
        $data_m = json_decode($resp->getBody()->getContents());
      
        return $response->withJson($data_m)
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');

    });

    /**
     *@param{array, array}
     *Search function name by ids of movies
     */
    function findNamesGenre($arrayIdsGenre, $dataGenreNames){
        $itens[]="";
           
         foreach ($arrayIdsGenre as $key => $value1) {
           
            
            foreach ($dataGenreNames->genres as $key=> $value2) 
            {
                
                if($value2->id==$value1){
                   
                 
                   $itens[] =  $value2->name;
                 
                }
                  
            }   
            
        }

        return $itens;
        
    }
};
