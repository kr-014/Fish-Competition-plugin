<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$compid = $_GET['comp'];
$fish_catching_matrix = get_post_meta($compid, 'fish_catching_atrix', true);
$Bonus_points = get_post_meta($compid, 'Bonus_points', true);
$competition_settings = get_post_meta($compid, 'competition_settings', true);
$competition_othere_settings = get_post_meta($compid, 'competition_othere_settings', true);

$args = array(
	//'numberposts' => $competition_settings['top_winners'],
	'numberposts' => -1,
	'post_type'   => 'fishapp-participants',
	
	'meta_query' => array(
        array(
            'key' => 'participant_comp_details',
			'value' => $compid,
			'compare' => '=',
        )
    )
  );

  
   
$winners = get_posts( $args ); 
$sortArray = array();
?>

<?php foreach($winners as $winkey => $winner){ 
	$parti_user = get_user_by('id', get_post_meta($winner->ID,'Participant_list',true)); 
	$point_cal = array();
	if(!empty(get_post_meta($winner->ID, 'participant_upload_image',true))){

		foreach(get_post_meta($winner->ID, 'participant_upload_image',true) as $valimg){
			if(get_post_meta($valimg, "approve_status", true) == 'approved') {
				$field_Fish_lenght_get = get_post_meta($valimg, "Fish_lenght_get", true);

				if($fish_catching_matrix['length']<= $field_Fish_lenght_get){
					if($field_Fish_lenght_get <=20) {
						$point_cal[] = $field_Fish_lenght_get*1.5;

					} else if($field_Fish_lenght_get > 20){
						$extra_len = ($field_Fish_lenght_get-20)*$Bonus_points['fish_length_matrix'];

						$point_cal[] = $extra_len+30;

					} else {
						$point_cal[] = 0;
					}
				} 
			}
			
		}
	}
	if(!empty(get_post_meta($winner->ID, 'participant_upload_videos',true))){
		foreach(get_post_meta($winner->ID, 'participant_upload_videos',true) as $valvid) {
			if(get_post_meta($valvid, "approve_status", true) == 'approved') {
				$point_cal[] = $Bonus_points['fish_releases_point'];
			}
		}
	}
	$tabledata[$winkey]['rank'] = $winkey+1;
	$tabledata[$winkey]['participant'] = '<img src="'.get_avatar_url($author_id).'" width="50px"/>'.$parti_user->display_name;
	$tabledata[$winkey]['competition'] = get_the_title(get_post_meta($winner->ID,'participant_comp_details',true));
	$tabledata[$winkey]['point'] = array_sum($point_cal);
	$tabledata[$winkey]['action'] = '<a href="#" class="view_activity">View</a><a href="#">View</a>';
}

foreach ($tabledata as $key => $row)
{
    $vc_array_name[$key] = $row['point'];
}
array_multisort($vc_array_name, SORT_DESC, $tabledata);
?>



<div class="drawer-compitition">
	<div class="container_drawer">
		<div class="header_drawer_part">
			<?php 
				if(get_post_meta($compid,'option_compi_end_date',true)==1){
					echo '<div class="status Stop"> Stop </div>';
				} else {
					if(strtotime($competition_othere_settings['compi_start_date']) > time()) {

						$to_date = time(); // Input your date here e.g. strtotime("2014-01-02")
						$from_date = strtotime($competition_othere_settings['compi_start_date']);
						$day_diff = abs($to_date - $from_date);
						echo '<div class="status not_started"> coming ('.floor($day_diff/(60*60*24)).' days )</div>';
					} else {
						if(strtotime($competition_othere_settings['compi_end_date']) > time()){
							echo '<div class="status running"> Running </div>';
						} else {
							echo '<div class="status completed"> Completed </div>';
						}
						
					}
				}
			?>
			<h1><?php echo get_the_title($compid); ?></h1>
			<h4>Start: <?php echo $competition_othere_settings['compi_start_date']; ?> - End: <?php echo $competition_othere_settings['compi_end_date']; ?></h4>
			<div class="compi_header_part">
				<div class="col-6 compi_header_part_left">
					<h4>Fish biology: </h4>
					<p><?php echo $fish_catching_matrix['fish_biology'] ?></p>
					<h4>Fish Lenght: <?php echo $fish_catching_matrix['length'] ?></h4>
				</div>
				<div class="col-6 compi_header_part_right">
					<h4>Point Rules: </h4>
					<ul>
						<li> 1.5 Points on 20 Inches of fish on increment of every inches  there would be a increment of <?php echo $Bonus_points['fish_length_matrix'] ?> Points</li>
						<li> Release fish Point <?php echo $Bonus_points['fish_releases_point']; ?></li>
					</ul>
					
				</div>
			</div>
		</div>
		<div class="content_drawer_part">
			<div class="compi_leader_part">
					<table class="compi_leaderboard_table">
						<thead>
							<th class="rank">Rank</th>
							<th class="participant">Name</th>
							<th class="competition">Competition</th>
							<th class="point">Points</th>
							<th class="action">Action</th>
						</thead>
						<tbody>
							<?php foreach(array_slice($tabledata,0,$competition_settings['top_winners']) as $datakey => $data){?>
							
					
								<tr>
									<td class="rank"><span><?php echo $datakey+1; ?></span></td>
									<td class="participant"><?php echo $data['participant']; ?></td>
									<td class="competition"><?php echo $data['competition']; ?></td>
									<td class="point"><?php echo $data['point']; ?></td>
									<td class="action"><?php echo $data['action']; ?></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				
			</div>
		</div>
	</div>
</div>