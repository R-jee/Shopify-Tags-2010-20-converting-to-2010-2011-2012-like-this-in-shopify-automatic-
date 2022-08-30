
function createTags__withGraphQL( $access_token , $host_shop , $gid , $tags_array ){
    $queryUsingVariables = <<<QUERY
    mutation addTags(\$id: ID!, \$tags: [String!]!) {
        tagsAdd(id: \$id, tags: \$tags) {
            node {
                id
            }
            userErrors {
                message
            }
        }
    }
    QUERY;
    $variables = [
        "id" => $gid,
        "tags" => $tags_array
    ];
    shopify_graphQL_call($access_token , $host_shop , "2022-04", ['query' => $queryUsingVariables, 'variables' => $variables] );
}

// $test = shopify_graphQL_call($access_token , $host_shop , "2022-04", ["query" => $query, "variables" => $variables]);

$graphql___tags_getAll = array(
    "query" => '{
        products(first: 1) {
            edges {
                cursor
                node {
                    id
                    tags
                }
            }
            pageInfo {
                hasNextPage
                hasPreviousPage
                startCursor
                endCursor
            }
        } 
    }'
);


$tags________prod = shopify_graphQL_call($access_token , $host_shop , "2022-04" , $graphql___tags_getAll );
echo "<pre>";
// print_r($tags________prod['response']);
// print_r( json_decode($tags________prod['response'])->data->products->edges );
// print_r( json_decode($tags________prod['response'])->data->products->pageInfo->hasNextPage );

$next_ = json_decode($tags________prod['response'])->data->products->pageInfo->hasNextPage;
$next_node = "";

while( $next_ == 1 ){
    $next_node = json_decode($tags________prod['response'])->data->products->pageInfo->endCursor;

    $gql_Tags_getNEXT = array(
        "query" => '{
            products(first: 1, after: "'. $next_node .'" ) {
                edges {
                    cursor
                    node {
                        id
                        tags
                    }
                }
                pageInfo {
                    hasNextPage
                    hasPreviousPage
                    startCursor
                    endCursor
                }
            } 
        }'
    );

   
    $tags________prod = shopify_graphQL_call($access_token , $host_shop , "2022-04" , $gql_Tags_getNEXT );
    $next_ = json_decode($tags________prod['response'])->data->products->pageInfo->hasNextPage;
    // print_r( json_decode($tags________prod['response'])->data->products->edges );
    $product = ( json_decode($tags________prod['response'])->data->products->edges[0] );
    $tags = $product->node->tags;
    $prod_gid = $product->node->id;
    // print_r($tags);
    // die();
    foreach ($tags as $key => $t) {
        $temp_tag = $t;
        $tags_array = array();
        if( strpos($temp_tag, "-") !== false ) {
            $t___tag = str_replace("-", "" , $t);
            if(is_numeric( $t___tag )){
                // echo " this is an integer ";
                // echo $temp_tag;
                // die();
                $temp_split_tag = explode("-", $temp_tag);
                // print_r( $temp_split_tag );
                // die();
                ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                
                if( strlen( $temp_split_tag[0] ) == 4 &&  strlen( $temp_split_tag[1] ) == 4 ){
                    
                    $first = $temp_split_tag[0];
                    $second = $temp_split_tag[1];
                    $larger_year = 0;
                    $smaller_year = 0;
                    if( $first > $second ){
                        $larger_year = $first;
                        $smaller_year = $second;
                    }elseif( $second > $first ){
                        $larger_year = $second;
                        $smaller_year = $first;
                    }
                    $start = $smaller_year;
                    $end = $larger_year;
                    array_push( $tags_array , $smaller_year );
                    array_push( $tags_array , $larger_year );
                    
                    for ($i=0; $i < ($larger_year - $smaller_year) ; $i++) { 
                        // array_push( $tags_array , ($start + ($i + 1)) );
                        $tags_array[] = ($start + ($i + 1)) ;
                    }
                    createTags__withGraphQL( $access_token , $host_shop , $prod_gid , Implode( ',' , $tags_array) );
                    // print_r (Implode( ',' , $tags_array)  );
                    // die();
                }
                
                ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                if( strlen( $temp_split_tag[0] ) == 2 ||  strlen( $temp_split_tag[1] ) == 2 ){
                    $first = 0;
                    $temp_second = 0;
                    $second = 0;

                    $larger_year = 0;
                    $smaller_year = 0;
                    if( strlen( $temp_split_tag[1] ) == 4 ){
                        $first = $temp_split_tag[1];
                        $temp_second = $temp_split_tag[0];
                        if( substr($first , 0,2) == 20 ){
                            $second = $temp_second + 2000;
                        }elseif( substr($first , 0,2) == 19 ){
                            $second = $temp_second + 1900;
                        }
                        
                    }elseif( strlen( $temp_split_tag[1] ) == 2 ){
                        $first = $temp_split_tag[0];
                        $temp_second = $temp_split_tag[1];
                        if( substr($first , 0,2) == 20 ){
                            $second = $temp_second + 2000;
                        }elseif( substr($first , 0,2) == 19 ){
                            $second = $temp_second + 1900;
                        }
                    }

                    if( $first > $second ){
                        $larger_year = $first;
                        $smaller_year = $second;
                    }elseif( $second > $first ){
                        $larger_year = $second;
                        $smaller_year = $first;
                    }
                    $start = $smaller_year;
                    $end = $larger_year;
                    array_push( $tags_array , $smaller_year );
                    array_push( $tags_array , $larger_year );
                    
                    for ($i=0; $i < ($larger_year - $smaller_year) ; $i++) { 
                        // array_push( $tags_array , ($start + ($i + 1)) );
                        $tags_array[] = ($start + ($i + 1)) ;
                    }
                    createTags__withGraphQL( $access_token , $host_shop , $prod_gid , Implode( ',' , $tags_array) );
                    // print_r (Implode( ',' , $tags_array)  );

                }
                ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////


            }
        }

    }

}
