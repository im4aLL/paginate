<?php
require_once __DIR__ . '/../vendor/autoload.php';

$config = [
    'host' => 'localhost',
    'name' => 'abworkout',
    'username' => 'root',
    'password' => '',
];

$db = new \Hadi\Database();
$db->connect($config);

$total = $db->query("SELECT id FROM orders")->get();

$paginate = new \Hadi\Paginate([
    'per_page' => 1,
    'page_param' => 'page',
    'page_url' => 'http://localhost/paginate/test/',
    'total_record' => count($total),
]);

$orders = $db->query("SELECT * FROM orders ".$paginate->limit())->get();

echo $paginate->limit().' <br><br>';

foreach($orders as $order) {
    echo $order->id.' - ';
    echo $order->order_number;
    echo '<br>';
}

$db->disconnect();
?>

<br><br>
Total records: <?= $paginate->totalRecord() ?>
<hr>
Page <?= $paginate->currentPage() ?> of <?= $paginate->totalPage() ?>
<hr>

<a href="<?= $paginate->previousPageUrl() ?>">Previous page</a>
<a href="<?= $paginate->nextPageUrl() ?>">Next page</a>

<hr>

<a href="<?= $paginate->firstPageUrl() ?>">First page</a>
<a href="<?= $paginate->lastPageUrl() ?>">Last page</a>

<hr>

<?php
foreach($paginate->pages() as $page) {
    if($page['number']) {
        echo ' <a href="'.$page['url'].'">'.$page['number'].'</a> ';
    }
    else {
        echo ' .... ';
    }
}

