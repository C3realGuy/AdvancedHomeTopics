<?php
/**
 * Displays the custom homepage. Hack away!
 *
 * Wedge (http://wedge.org)
 * Copyright © 2010 René-Gilles Deberdt, wedge.org
 * License: http://wedge.org/license/
 */

function template_main()
{
	// Nothing to say!
}

function explodeToIntegers($delimiter, $str) {
	if (strpos($str, $delimiter) === false) {
		return (int) $str;
	}
	$integers = [];
	foreach (explode($delimiter, $str) as $prep) {
		$integers[] = (int) $prep;
	}
	return $integers;
}

function nextStep($steps, $n) {
	sort($steps);
	foreach ($steps as $step) {
		if( $n < $step) return $step;
	}
	// Otherwise return last element
	return array_values(array_slice($steps, -1))[0];;
}

$nc = 0; // Counter for home_topics. If we have more than one, we want to known
         // which one we are.

function template_home_topics($args)
{
	global $txt, $nc;

	log_error($args);
	$n = 0;
	$title = $txt['recent_posts'];
	$include_boards = [];
	$exclude_boards = [];
	$show_board_index = true;
	$steps = [5, 10, 20, 50, 100];

	if(isset($args)) {
		$args = explode('|', $args);
		if(isset($args[0])) {
			$n = (int) $args[0];
		}
		if(isset($args[1])) {
			$title = $args[1];
		}
		if(!empty($args[2]) ) {
			$include_boards = explodeToIntegers(';', $args[2]);
		}
		if(!empty($args[3])) {
			$exclude_boards = explodeToIntegers(';', $args[3]);
		}
		if(!empty($args[4])) {
			$show_board_index = filter_var($args[4], FILTER_VALIDATE_BOOLEAN);
		}
		if(!empty($args[5])) {
			$steps = explodeToIntegers(';', $args[5]);
		}
	}

	$n = isset($_REQUEST['n'.$nc]) ? (int) $_REQUEST['n'.$nc ] : ($n ?: 5);
	$next = nextStep($steps, $n);
	log_error(var_export([$nc, $steps, $args], true));

	// Build next_url.
	$GET = $_GET; // COPY $_GET
	$GET['n'.$nc] = $next; // Set current n.nc to $next
	$next_url = '<URL>?'.http_build_query($GET, null, '&'); // build next_url

	echo '
	<we:cat style="margin-top: 16px">', $n == $next ? '' : ($show_board_index == true ? '
		<span class="floatright"><a href="<URL>?action=boards">' . $txt['board_index'] . '</a></span>' : ''), '
		', $title, '
		<a href="'.$next_url.'" class="middle" style="display: inline-block; height: 16px"><div class="floatleft foldable"></div></a>', '
	</we:cat>
	<we:block class="tborder wide" style="padding: 2px; border: 1px solid #dcc; border-radius: 5px">
		<table class="homeposts cs0">';

	loadSource('../SSI');
	$boards = ssi_recentTopicTitles($n, $exclude_boards, $include_boards, 'naos');
	$nb_new = get_unread_numbers($boards);

	$alt = '';
	$is_mobile = we::is('mobile');
	foreach ($boards as $post)
	{
		$alt = $alt ? '' : '2';
		echo '
			<tr class="windowbg', $alt, '">', $is_mobile ? '' : '
				<td class="latestp1">
					<div>' . $post['time'] . ' ' . $txt['by'] . ' ' . $post['poster']['link'] . '</div>
				</td>', '
				<td class="latestp2">', $is_mobile ? '
					' . $post['time'] . ' ' . $txt['by'] . ' ' . $post['poster']['link'] . '<br>' : '', '
					', $post['board']['link'], ' &gt; ';

		if ($post['is_new'] && we::$is_member)
			echo isset($nb_new[$post['topic']]) ? '<a href="' . $post['href'] . '" class="note">' . $nb_new[$post['topic']] . '</a> ' : '';

		echo '<a href="', $post['href'], '">', $post['subject'], '</a>
				</td>
			</tr>';
	}

	echo '
		</table>
	</we:block>';
	$nc++;
}

// Output a custom introduction. HTML is accepted, unfiltered.
function template_home_blurb()
{
	global $settings;

	if (isset($settings['homepage_blurb_' . we::$user['language']]))
		$lang = we::$user['language'];
	elseif (isset($settings['homepage_blurb_' . $settings['language']]))
		$lang = $settings['language'];
	else
		return;

	if (!SKIN_MOBILE)
	{
		if (!empty($settings['homepage_blurb_title_' . $lang]))
			echo '
	<we:cat class="wtop">
		', $settings['homepage_blurb_title_' . $lang], '
	</we:cat>';

		echo '
	<div class="windowbg2 wide home-intro">
		<div class="wrc">', str_replace("\n", '<br>', $settings['homepage_blurb_' . $lang]), '</div>
	</div>';
	}
}
