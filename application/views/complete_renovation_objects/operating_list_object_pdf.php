<style>
	th {
		font-weight: bold;
	}
</style>

<h1 align="center">Експлуатаційна відомість</h1>
<h1 align="center"><?php echo $complete_renovation_object->name; ?></h1>

<table border="1" cellpadding="5">
	<thead>
		<tr align="center">
			<th width="5%">№ з/п</th>
			<th width="10%">Дата</th>
			<th width="15%">Вид обслуговування</th>
			<th width="55%">Перелік робіт</th>
			<th width="15%">Виконавець</th>
		</tr>
	</thead>
	<tbody>
		<?php $i = 1;
		foreach ($results as $item) : ?>
			<tr nobr="true">
				<td width="5%" align="center"><?php echo $i; ?></td>
				<td width="10%" align="center"><?php echo $item->service_date; ?></td>
				<td width="15%"><?php echo $item->type_service_short_name ? $item->type_service_short_name : '-'; ?></td>
				<td width="55%"><?php echo $item->service_data; ?></td>
				<td width="15%"><?php echo $item->executor; ?></td>
			</tr>
		<?php $i++;
		endforeach; ?>
	</tbody>
</table>
<br />
<br />
<br />
<table>
	<tr>
		<td width="40%" align="right"><strong><?php echo $this->session->user->position; ?></strong></td>
		<td width="30%"></td>
		<td width="30%">
			<strong><?php echo $this->session->user->name . ' ' . mb_strtoupper($this->session->user->surname); ?></strong>
		</td>
	</tr>
</table>
