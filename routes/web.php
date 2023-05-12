<?php


Route::get('/backend/{path}.{ex}',function (){


    return "123";

})->where(['path'=>"[/A-Za-z0-9.]+",'ex'=>"(jpg|jpeg|png|gif|webp){1}"]);
