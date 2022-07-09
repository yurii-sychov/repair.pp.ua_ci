<style>
	td {
		border: 1px solid #000000;
	}

	.group th {
		font-weight: bold;
		border-bottom: 1px solid #000000;
		letter-spacing: 1px;
	}

	.no-group th {
		font-weight: bold;
		border: 1px solid #000000;
	}
</style>
<h1 align="center">ПАСПОРТ ЕНЕРГЕТИЧНОГО ОБ`ЄКТА <span style="color: green">(Процес розробки)</span></h1>
<h5 align="center" style="color: green">Пропозиції та зауваження надавати О. ГОРДІЄНКО (тел. 15-45)</h5>
<h2 align="center"><?php echo $complete_renovation_object->name; ?></h2>

<?php foreach ($results as $group => $equipments) : ?>
	<table border="0" cellpadding="5" align="center">
		<thead>
			<tr class="group">
				<th colspan="5">
					<h3><?php echo $equipments[0]['equipment']; ?><small><?php echo ' (' . count($equipments) . ' од.)'; ?></small></h3>
				</th>
			</tr>
			<tr class="no-group">
				<th width="<?php echo $equipments[0]['id_w']; ?>">№ з/п</th>
				<th width="<?php echo $equipments[0]['disp_w']; ?>">Дисп. назва<br />(місце)</th>
				<th width="<?php echo $equipments[0]['type_w']; ?>">Тип обладнання<br />(ізоляція)</th>
				<th width="<?php echo $equipments[0]['number_w']; ?>">Зав. №</th>
				<th width="<?php echo $equipments[0]['year_w']; ?>">Рік виг.</th>
				<th width="40%">Основні технічні характеристики</th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 1;
			foreach ($equipments as $item) : ?>
				<tr style="color: <?php echo $item['color']; ?>;" nobr="true">
					<td width="<?php echo $item['id_w']; ?>">
						<?php echo $i; ?>
					</td>
					<td width="<?php echo $item['disp_w']; ?>" style="text-align: center;" bgcolor="<?php echo $item['disp_bc']; ?>">
						<?php echo $item['disp'] . '<br /><small>(' . $item['place'] . ')</small>'; ?>
					</td>
					<td width="<?php echo $item['type_w']; ?>" style="text-align: center;" bgcolor="<?php echo $item['type_bc']; ?>">
						<?php echo $item['type'] . '<br /><small>(' . $item['insulation_type'] . ')</small>'; ?>
					</td>
					<td width="<?php echo $item['number_w']; ?>" bgcolor="<?php echo $item['number_bc']; ?>">
						<?php echo $item['number']; ?>
					</td>
					<td width="<?php echo $item['year_w']; ?>" bgcolor="<?php echo $item['year_bc']; ?>">
						<?php echo $item['year']; ?>
					</td>
					<td width="40%" style="text-align: left;">
						<ul>
							<li><strong>характеристика: </strong>значення</li>
							<li><strong>характеристика: </strong>значення</li>
							<li><strong>характеристика: </strong>значення</li>
						</ul>
					</td>
				</tr>
			<?php $i++;
			endforeach; ?>
		</tbody>
	</table>
	<br />
	<br />
<?php endforeach; ?>
