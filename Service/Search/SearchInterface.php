<?php


namespace Ycore\Service\Search;


interface SearchInterface
{



    function __construct($allModelList);


    function AlertSearch(string $model,array $condition,array $label,int $page=1,array $defaultCondition=[],string $namespace='\App\Models\\',$with=[]):array ;

}
