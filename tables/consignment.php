<?php
require_once '../config.php';
//приходить з індекса чи кропа
$sell_id = $_SESSION['sell_id'];
//обрано на сторінці
$chose_id=$_SESSION['chose_id'];

$sort_list = array(
	'id_asc'=>'`id`',
	'id_desc'=>'`id` DESC',
	'crop_name_asc'=>'`crop_name`',
	'crop_name_desc'=>'`crop_name` DESC',
	'amount'=>'`amount`',
	'amount_desc'=>'`amount` DESC',
	'date_asc'=>'`date`',
	'date_desc'=>'`date` DESC',
	'name_asc'=>'`name`',
	'name_desc'=>'`name` DESC',
	'number_asc'=>'`number`',
	'number_desc'=>'`number` DESC',
	'moisture_asc'=>'`moisture`',
	'moisture_desc'=>'`moisture` DESC',
	'garbage_asc'=>'`garbage`',
	'garbage_desc'=>'`garbage` DESC',
	'minerals_asc'=>'`minerals`',
	'minerals_desc'=>'`minerals` DESC',
	'nature_asc'=>'`nature`',
	'nature_desc'=>'`nature` DESC',
);
$sort = @$_GET['sort'];
if (array_key_exists($sort, $sort_list)){
	$sort_sql = $sort_list[$sort];
} else {
	$sort_sql=reset($sort_list);
}

$result = $dbConnect->query("SELECT `Consignment_OUT`.`id` AS `id`,`Consignment_OUT`.`Crop_id` AS `Crop_id`,`Crop`.`name` AS `crop_name`,`Consignment_OUT`.`amount` AS `amount`,`Consignment_OUT`.`date` AS `date`,`Consignment_OUT`.`name` AS `name`,`Consignment_OUT`.`number` AS `number`,`Consignment_OUT`.`moisture` AS `moisture`,`Consignment_OUT`.`garbage` AS `garbage`,`Consignment_OUT`.`minerals` AS `minerals`,`Consignment_OUT`.`nature` AS `nature` from (`Consignment_OUT` join `Crop` on((`Consignment_OUT`.`Crop_id` = `Crop`.`id`))) {$sort_sql}");

$list = $result->fetchAll(PDO::FETCH_ASSOC);

$fields=["crop_name","amount","date","name","number","moisture","garbage","minerals","nature"];

if (isset($_POST['ok'])){
	if (!empty($_POST)){

		$crop_ui=htmlspecialchars($_POST['crop_select']);
		$amount_ui=htmlspecialchars($_POST['amount']);
		$date_ui=htmlspecialchars($_POST['date']);
		$name_ui=htmlspecialchars($_POST['name']);
		$number_ui=htmlspecialchars($_POST['number']);
		$moisture_ui=htmlspecialchars($_POST['moisture']);
		$garbage_ui=htmlspecialchars($_POST['garbage']);
		$minerals_ui=htmlspecialchars($_POST['minerals']);
		$nature_ui=htmlspecialchars($_POST['nature']);

		$error=[];
		foreach ($_POST as $k => $v) {
			if (in_array($k, $fields) && empty($v)){
				$error[$k]="field must be filled!";
			}
		}
			//проверка на непустые поля
		if (empty($error)){
			if (empty($chose_id)){
				$stmt = $dbConnect->prepare("INSERT INTO `Consignment_OUT` (
					`Crop_id`,
					`amount`,
					`date`,
					`name`,
					`number`,
					`moisture`,
					`garbage`,
					`minerals`,
					`nature`
					) 
				VALUES 
				( 
					:c_id,  
					:a,
					:d,
					:n,
					:nu,
					:mo,
					:ga,
					:mi,
					:na
				)"
			);
				$stmt->execute(["c_id"=>$crop_ui,"a"=>$amount_ui,"d"=>$date_ui,"n"=>$name_ui,"nu"=>$number_ui,"mo"=>$moisture_ui,"ga"=>$garbage_ui,"mi"=>$minerals_ui,"na"=>$name_ui]);
			}else{
				$stmt = $dbConnect->prepare("UPDATE `Consignment_OUT`
					SET
					`Crop_id`=:c_id,
					`amount`=:a,
					`date`=:d,
					`name`=:n,
					`number`=:nu,
					`moisture`=:mo,
					`garbage`=:ga,
					`minerals`=:mi,
					`nature`=:na
					where `id`=:id");
				$stmt->execute(["c_id"=>$crop_ui,"a"=>$amount_ui,"d"=>$date_ui,"n"=>$name_ui,"nu"=>$number_ui,"mo"=>$moisture_ui,"ga"=>$garbage_ui,"mi"=>$minerals_ui,"na"=>$name_ui,"id"=>$chose_id]);
			}
		}
	}
	require_once TEMPLATES_PATH."consignment.php";
}

if (!empty($chose_id)){
	foreach ($list as $key => $value) {
		if ($value['id']===$chose_id){
			$crop_ui=$value['Crop_id'];
			$amount_ui=$value['amount'];
			$date_ui=$value['date'];
			$name_ui=$value['name'];
			$number_ui=$value['number'];
			$moisture_ui=$value['moisture'];
			$garbage_ui=$value['garbage'];
			$minerals_ui=$value['minerals'];
			$nature_ui=$value['nature'];
		}
	}
}

if (isset($_POST['clear'])){
	//Сделать реальную очистку chose_id
	//$chose_id="";
	$crop_ui="";
	$amount_ui="";
	$date_ui="";
	$name_ui="";
	$number_ui="";
	$moisture_ui="";
	$garbage_ui="";
	$minerals_ui="";
	$nature_ui="";
}

if (isset($_POST['delete'])){
	$delete = $dbConnect->prepare("DELETE from Consignment_OUT where id = :id");
	$delete->execute(["id"=>$chose_id]);
}

function sort_link_th($title, $a, $b) {
	$sort = @$_GET['sort'];
	if ($sort == $a) {
		return '<a class="active" href="?sort=' . $b . '">' . $title . ' <i></i></a>';
	} elseif ($sort == $b) {
		return '<a class="active" href="?sort=' . $a . '">' . $title . ' <i></i></a>';  
	} else {
		return '<a href="?sort=' . $a . '">' . $title . '</a>';  
	}
}

require_once TEMPLATES_PATH."consignment.php";
?>