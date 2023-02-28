<?php
require_once '../config.php';
//приходить з індекса чи кропа
if (!empty($_SESSION['sell_id'])){
	$sell_id=$_SESSION['sell_id'];
}

//обрано на сторінці
if (isset($_POST['consignment_chose_id'])){
	$_SESSION['consignment_chose_id']=$_POST['consignment_chose_id'];
}

if (!empty($_SESSION['consignment_chose_id'])){
	$chose_id=$_SESSION['consignment_chose_id'];
}

$result = $dbConnect->query("select * FROM `Crop` where `amount` > 0");
$crops_all = $result->fetchAll(PDO::FETCH_ASSOC);

$result = $dbConnect->query("select `Consignment_OUT`.`id` AS `id`,`Consignment_OUT`.`Crop_id` AS `Crop_id`,`Crop`.`name` AS `crop_name`,`Consignment_OUT`.`amount` AS `amount`,`Consignment_OUT`.`date` AS `date`,`Consignment_OUT`.`name` AS `name`,`Consignment_OUT`.`number` AS `number`,`Consignment_OUT`.`moisture` AS `moisture`,`Consignment_OUT`.`garbage` AS `garbage`,`Consignment_OUT`.`minerals` AS `minerals`,`Consignment_OUT`.`nature` AS `nature` from (`Consignment_OUT` join `Crop` on((`Consignment_OUT`.`Crop_id` = `Crop`.`id`)))");
$list = $result->fetchAll(PDO::FETCH_ASSOC);

$fields=["crop_name","amount","name","number","moisture","garbage","minerals","nature"];

if (isset($_POST['ok'])){
	if (!empty($_POST)){

		$crop_ui=htmlspecialchars($_POST['crop_select']);
		$amount_ui=htmlspecialchars($_POST['amount']);
		$name_ui=htmlspecialchars($_POST['name']);
		$number_ui=htmlspecialchars($_POST['number']);
		$moisture_ui=htmlspecialchars($_POST['moisture']);
		$garbage_ui=htmlspecialchars($_POST['garbage']);
		$minerals_ui=htmlspecialchars($_POST['minerals']);
		$nature_ui=htmlspecialchars($_POST['nature']);

		$error=[];
		foreach ($_POST as $k => $v) {
			if (in_array($k, $fields) && empty($v)){
				$error[$k]="Поле має бути заповнене!";
			}
		}

		foreach($crops_all as $k=>$v){
			if ($v['id']==$crop_ui){
				if ($v['amount']<$amount_ui){
					$error['amount']="Продати більше ніж зберігається не можна";
				}
			}
		}

			//проверка на непустые поля
		if (empty($error)){
			if (empty($chose_id)){
				$stmt = $dbConnect->prepare("INSERT INTO `Consignment_OUT` (
					`Crop_id`,
					`amount`,
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
					:n,
					:nu,
					:mo,
					:ga,
					:mi,
					:na
				)"
			);
				$stmt->execute(["c_id"=>$crop_ui,"a"=>$amount_ui,"n"=>$name_ui,"nu"=>$number_ui,"mo"=>$moisture_ui,"ga"=>$garbage_ui,"mi"=>$minerals_ui,"na"=>$name_ui]);
				header("Refresh:0");
			}else{
				$stmt = $dbConnect->prepare("UPDATE `Consignment_OUT`
					SET
					`Crop_id`=:c_id,
					`amount`=:a,
					`name`=:n,
					`number`=:nu,
					`moisture`=:mo,
					`garbage`=:ga,
					`minerals`=:mi,
					`nature`=:na
					where `id`=:id");
				$stmt->execute(["c_id"=>$crop_ui,"a"=>$amount_ui,"n"=>$name_ui,"nu"=>$number_ui,"mo"=>$moisture_ui,"ga"=>$garbage_ui,"mi"=>$minerals_ui,"na"=>$name_ui,"id"=>$chose_id]);
				header("Refresh:0");
			}
		}
	}
}

if (!empty($chose_id)){
	foreach ($list as $key => $value) {
		if ($value['id']==$chose_id){
			$crop_ui=$value['Crop_id'];
			$amount_ui=$value['amount'];
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
	$_SESSION['sell_id']="";
	$_SESSION['consignment_chose_id']="";
	$crop_ui="";
	$amount_ui="";
	$name_ui="";
	$number_ui="";
	$moisture_ui="";
	$garbage_ui="";
	$minerals_ui="";
	$nature_ui="";
	header("Refresh:0");
}

if (isset($_POST['delete'])){
	$delete = $dbConnect->prepare("DELETE from Consignment_OUT where id = :id");
	$delete->execute(["id"=>$chose_id]);
	header("Refresh:0");
}

require_once TEMPLATES_PATH."consignment.php";
?>