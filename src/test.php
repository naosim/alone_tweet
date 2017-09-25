<?php
include_once "./EzTest.php";

include_once "./php/domain.php";
include_once "./php/datasource.php";
include_once "./php/service.php";

test('File', [
  test('get_file_name', [
    function($assertThat) { $assertThat((new File('./path/to/file/name.txt'))->get_file_name(), 'name.txt'); },
    function($assertThat) { $assertThat((new File('./path/to/file/name'))->get_file_name(), null); },
    function($assertThat) { $assertThat((new File('name.txt'))->get_file_name(), 'name.txt'); },
    function($assertThat) { $assertThat((new File('name'))->get_file_name(), null); },
  ]),
  test('get_dir_path', [
    function($assertThat) { $assertThat((new File('./path/to/file/name.txt'))->get_dir_path(), './path/to/file'); },
    function($assertThat) { $assertThat((new File('./path/to/file'))->get_dir_path(), './path/to/file'); },
    function($assertThat) { $assertThat((new File('dir'))->get_dir_path(), 'dir'); },
  ]),
])->run();
