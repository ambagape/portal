<?php

// need this one to serve angular
Route::view('{any?}', 'angular')->where('all', '^((?!api/v1).)*');

