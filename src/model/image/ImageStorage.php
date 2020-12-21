<?php

interface ImageStorage {
    public function readAll($id);
    public function checkImage($data, $starId);
    public function checkUpdateImage($data, $starId);
    public function delete($id);
    public function create(Image $i);
    
}

?>