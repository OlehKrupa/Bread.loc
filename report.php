<?php 
require_once "config.php";

require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();

if (isset($_POST['crop_report'])){
	$stmt = $dbConnect->query("SELECT
		Crop.id,
		Supplier.`name` as `supplier_name`,
		Crop.date,
		Warehouse.`name` as `warehouse_name`,
		Crop.amount,
		Standard.`name` as `standard_name`,
		Crop.`name`,
		Crop.variety,
		Crop.grade,
		Crop.moisture,
		Crop.garbage,
		Crop.minerals,
		Crop.nature 
		FROM
		Crop
		INNER JOIN Standard ON Crop.Standard_id = Standard.id
		INNER JOIN Warehouse ON Crop.Warehouse_id = Warehouse.id
		INNER JOIN Supplier ON Crop.Supplier_id = Supplier.id

		where amount>0");
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// Add a new worksheet and set its title
$worksheet = $spreadsheet->getActiveSheet();
$worksheet->setTitle('Crop Data');

// Write the column headers in the first row
$worksheet->setCellValue('A1', 'ID');
$worksheet->setCellValue('B1', 'Supplier Name');
$worksheet->setCellValue('C1', 'Date');
$worksheet->setCellValue('D1', 'Warehouse Name');
$worksheet->setCellValue('E1', 'Amount');
$worksheet->setCellValue('F1', 'Standard Name');
$worksheet->setCellValue('G1', 'Name');
$worksheet->setCellValue('H1', 'Variety');
$worksheet->setCellValue('I1', 'Grade');
$worksheet->setCellValue('J1', 'Moisture');
$worksheet->setCellValue('K1', 'Garbage');
$worksheet->setCellValue('L1', 'Minerals');
$worksheet->setCellValue('M1', 'Nature');

// Loop through the data and write it to the worksheet
$row = 2;
foreach ($result as $data) {
    $worksheet->setCellValue('A' . $row, $data['id']);
    $worksheet->setCellValue('B' . $row, $data['supplier_name']);
    $worksheet->setCellValue('C' . $row, $data['date']);
    $worksheet->setCellValue('D' . $row, $data['warehouse_name']);
    $worksheet->setCellValue('E' . $row, $data['amount']);
    $worksheet->setCellValue('F' . $row, $data['standard_name']);
    $worksheet->setCellValue('G' . $row, $data['name']);
    $worksheet->setCellValue('H' . $row, $data['variety']);
    $worksheet->setCellValue('I' . $row, $data['grade']);
    $worksheet->setCellValue('J' . $row, $data['moisture']);
    $worksheet->setCellValue('K' . $row, $data['garbage']);
    $worksheet->setCellValue('L' . $row, $data['minerals']);
    $worksheet->setCellValue('M' . $row, $data['nature']);
    $row++;
}

// Create a new Xlsx writer object and save the file to the desktop
$writer = new Xlsx($spreadsheet);
$writer->save('~/Desktop/crop_data.xlsx');
}

if (isset($_POST['selled_crop'])){
	$stmt = $dbConnect->query("SELECT
		Crop.id,
		Supplier.`name` as `supplier_name`,
		Crop.date,
		Warehouse.`name` as `warehouse_name`,
		Crop.amount,
		Standard.`name` as `standard_name`,
		Crop.`name`,
		Crop.variety,
		Crop.grade,
		Crop.moisture,
		Crop.garbage,
		Crop.minerals,
		Crop.nature 
		FROM
		Crop
		INNER JOIN Standard ON Crop.Standard_id = Standard.id
		INNER JOIN Warehouse ON Crop.Warehouse_id = Warehouse.id
		INNER JOIN Supplier ON Crop.Supplier_id = Supplier.id

		where amount=0");
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	print_r($result);
}

if (isset($_POST['crop_critical_report'])){
	require_once "alert_cell.php";
	print_r($alert);
}

if (isset($_POST['standard_report'])){
	$stmt = $dbConnect->query("select * from Standard");
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	print_r($result);
}

if (isset($_POST['supplier_report'])){
	$stmt = $dbConnect->query("select * from Supplier");
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	print_r($result);
}

if (isset($_POST['warehouse_report'])){
	$stmt = $dbConnect->query("SELECT
		Warehouse.id,
		Warehouse.`name`,
		Warehouse.address,
		sum( Crop.amount ) AS occupancy,
		Warehouse.capacity,
		SUM( Crop.amount )/ Warehouse.capacity * 100 AS percent 
		FROM
		Warehouse
		INNER JOIN Crop ON Warehouse.id = Crop.Warehouse_id 
		GROUP BY
		Warehouse.id");
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	print_r($result);
}

if (isset($_POST['consignment_report'])){
	$date_start_ui= date('Y-m-d',strtotime('2000-01-01'));
	$date_end_ui=date('Y-m-d',strtotime('3000-01-01'));

	if (!empty($_POST['date_start'])){
		$date_start_ui=$_POST['date_start'];
	}
	if (!empty($_POST['date_end'])){
		$date_end_ui=$_POST['date_end'];
	}

	$select_consignment_ui=$_POST['select_consignment'];

	if($select_consignment_ui=="Crop"){
		$stmt = $dbConnect -> prepare("SELECT
			Crop.id,
			Supplier.`name` as `supplier_name`,
			Crop.date,
			Warehouse.`name` as `warehouse_name`,
			Crop.amount,
			Standard.`name` as `standard_name`,
			Crop.`name`,
			Crop.variety,
			Crop.grade,
			Crop.moisture,
			Crop.garbage,
			Crop.minerals,
			Crop.nature 
			FROM
			Crop
			INNER JOIN Standard ON Crop.Standard_id = Standard.id
			INNER JOIN Warehouse ON Crop.Warehouse_id = Warehouse.id
			INNER JOIN Supplier ON Crop.Supplier_id = Supplier.id

			where `Crop`.`date` BETWEEN :start_date and :end_date");

		$stmt -> execute(["start_date"=>$date_start_ui,"end_date"=>$date_end_ui]);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		print_r($result);
	}else{
		$stmt = $dbConnect ->prepare("SELECT
			Consignment_OUT.id,
			Crop.`name`,
			Crop.variety,
			Consignment_OUT.amount,
			Consignment_OUT.date,
			Consignment_OUT.`name` as `customer`,
			Consignment_OUT.moisture,
			Consignment_OUT.garbage,
			Consignment_OUT.minerals,
			Consignment_OUT.nature 
			FROM
			Consignment_OUT
			INNER JOIN Crop ON Consignment_OUT.Crop_id = Crop.id
			INNER JOIN Standard ON Crop.Standard_id = Standard.id
			INNER JOIN Supplier ON Crop.Supplier_id = Supplier.id
			INNER JOIN Warehouse ON Crop.Warehouse_id = Warehouse.id
			where `Consignment_OUT`.`date` BETWEEN :start_date and :end_date");

		$stmt -> execute(["start_date"=>$date_start_ui,"end_date"=>$date_end_ui]);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		print_r($result);
	}

	//Сделать вывод результа в excel

}
require_once TEMPLATES_PATH."report.php";
?>