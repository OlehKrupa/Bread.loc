<?php include TEMPLATES_PATH."partials".DIRECTORY_SEPARATOR."header.php";?>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
<link href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css" rel="stylesheet">

<div class="container-lg">
	<form action="/tables/consignment.php" method="post">
		<h1>Відправні накладні</h1>
		<div class="container-lg text-center row g-0">
			<div class="col px-2">
				<div class="row">
					<div class="col">
						<div class="input-group m-2">
							<span class="input-group-text" id="crop_select">Зерно</span>
							<select class="form-select <?php if(!empty($error['crop_select'])) echo 'is-invalid' ?>" id="crop_select" name="crop_select">
								<option value="" selected ></option>
								<?php if(!empty($crops_all)) foreach($crops_all as $key=>$value): ?>
								<option 
								value="<?php echo $value["id"];?>" 
								<?php if ((!empty($crop_ui))&&($value["id"]==$crop_ui)){echo "selected";}?> >
								<?php echo 
								"Код: ".
								$value["id"]." ".
								$value["name"]." ".
								$value["variety"]." ".
								$value["amount"]." тон |".
								$value["moisture"]."|".
								$value["garbage"]."|".
								$value["minerals"]."|".
								$value["nature"]."|";?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="col">
				<div class="input-group m-2">
					<span class="input-group-text" id="amount">Кількість</span>
					<input type="text" class="form-control <?php if(!empty($error['amount'])) echo 'is-invalid' ?>" placeholder="" name="amount" aria-describedby="amount" value="<?php if (!empty($amount_ui)){echo $amount_ui;} ?>">
					<div class="invalid-feedback"><?php echo $error['amount'] ?? '';?></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<div class="input-group m-2">
					<span class="input-group-text" id="name">Назва</span>
					<textarea class="form-control <?php if(!empty($error['name'])) echo 'is-invalid' ?>" id="name" name="name" aria-describedby="name"><?php if (!empty($name_ui)){echo $name_ui;} ?></textarea>
					<div class="invalid-feedback"><?php echo $error['name'] ?? '';?></div>
				</div>
			</div>
			<div class="col">
				<div class="input-group m-2">
					<span class="input-group-text" id="number">Телефон</span>
					<input type="text" class="form-control <?php if(!empty($error['number'])) echo 'is-invalid' ?>" placeholder="" name="number" aria-describedby="number" value="<?php if(!empty($number_ui)){echo $number_ui;} ?>">
					<div class="invalid-feedback"><?php echo $error['number'] ?? '';?></div>
				</div>
			</div>
		</div>
	</div>

</div>

<div class="container-lg text-center row g-0">
	<div class="col"><button class="btn btn-success mb-2" type="submit" name="ok" >Створити накладну</button></div>
	<div class="col"><button class="btn btn-secondary mb-2" type="submit" name="clear" >Очистити поля</button></div>
	<div class="col"><button class="btn btn-danger mb-2" type="submit" name="delete" >Видалити накладну</button></div>
</div>

</form>

<table id="table" class="table">
	<thead>
		<tr class="table-info">
			<th>Код</th>
			<th>Зерно</th>
			<th>Кількість</th>
			<th>Дата</th>
			<th>Назва</th>
			<th>Телефон</th>
			<th>Вологість</th>
			<th>Сміття</th>
			<th>PO₄</th>
			<th>Натура</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($list as $row): ?>
			<tr class="table-primary" onclick="get_element_by_click(<?php echo $row['id']; ?>)" > 
				<td><?php echo $row['id']; ?></td>
				<td><?php echo $row['crop_name']; ?></td>
				<td><?php echo $row['amount']; ?></td>
				<td><?php echo $row['date']; ?></td>
				<td><?php echo $row['name']; ?></td>
				<td><?php echo $row['number']; ?></td>
				<td><?php echo $row['moisture']; ?></td>
				<td><?php echo $row['garbage']; ?></td>
				<td><?php echo $row['minerals']; ?></td>
				<td><?php echo $row['nature']; ?></td>
			</tr >
		<?php endforeach; ?> 
	</tbody>
</table>

<script>
	$(document).ready( function () {
		var table = $('#table').DataTable({
			scrollY: '500px',
			scrollCollapse: true,
			paging: false,
		});
		$('#table tbody').on('click', 'tr', function () {
			var data = table.row(this).data();
			alert('Обрано накладну з кодом: ' + data[0]);

			$.ajax({
				type: "POST",
				url: "/tables/consignment.php",
				data: { consignment_chose_id: data[0]},
				success: function(response) {
					location.reload();
				},
				error: function(xhr, status, error) {
					console.error(error);
				}
			});
		});

	} );
</script>

</div>

<?php include TEMPLATES_PATH."partials".DIRECTORY_SEPARATOR."footer.php";?>