<?php
interface StarStorage {
    public function create(Star $s);
    public function read($id);
    public function readAll();
    public function update($id, Star $s);
    public function delete($id);
    public function deleteAll();
}
?>