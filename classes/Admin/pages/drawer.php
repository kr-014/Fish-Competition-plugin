<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$compid = $_GET['comp'];
$fish_catching_matrix = get_post_meta($compid, 'fish_catching_atrix', true);
$Bonus_points = get_post_meta($compid, 'Bonus_points', true);
$competition_settings = get_post_meta($compid, 'competition_settings', true);

$args = array(
	'numberposts' => $competition_settings['top_winners'],
	'post_type'   => 'fishapp-participants'
  );

  
   
$winners = get_posts( $args );
// print_r($winners);
?>

<div class="drawer-compitition">
	<div class="container_drawer">
		<div class="header_drawer_part">
			<h1><?php echo get_the_title($compid); ?></h1>
			<div class="compi_header_part">
				<div class="col-6">
					<h4>Fish biology: </h4>
					<p><?php echo $fish_catching_matrix['fish_biology'] ?></p>
					<h4>Fish Lenght: </h4>
				</div>
				<div class="col-6">
					<p><?php echo $fish_catching_matrix['length'] ?></p>
					<h4>Point Rules: </h4>
					<p> -> 1.5 Points on 20 Inches of fish on increment of every inches  there would be a increment of <?php echo $Bonus_points['fish_length_matrix'] ?> Points</p>
					<p> -> Release fish Point <?php echo $Bonus_points['fish_releases_point'] ?></p>
				</div>
			</div>
		</div>
		<div class="content_drawer_part">
			<div class="compi_leader_part">
					<table>
						<thead>
							<th>Rank</th>
							<th>Name</th>
							<th>Points</th>
							<th>Action</th>
						</thead>
						<tbody>
							<?php foreach($winners as $winkey => $winner){?>
								<tr>
									<td><?php echo $winkey; ?></td>
									<td><?php echo $winner->post_title; ?></td>
									<td>Points</td>
									<td>Action</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				
			</div>
		</div>
	</div>
</div>