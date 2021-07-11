<?php

if (!defined("IN_MYBB")) {
    die("Plik ten nie może być dostępny bezpośrednio.");
}
$plugins->add_hook('index_start', 'lastBansOnForum');

function lastBansOnForum_info() {
    return array(
        "name" => "5 ostatnich banów na forum",
        "description" => "Dodaje tabele 5 ostatnich banów pobieranych z amxbansa na forum. Plugin specjalnie napisany przez Poftorka dla PaintBallMod.com",
        "website" => "https://webboard.pl/user-36883.html",
        "author" => "Poftorek",
        "authorsite" => "https://webboard.pl/user-36883.html",
        "version" => "1.1",
        "compatibility" => "*"
    );
}

function lastBansOnForum_activate() {
    global $db, $mybb;

    $lastBansOnForum_group_settings = array(
        "gid" => NULL,
        "name" => "lastBansOnForum_option_category",
        "title" => "5 ostatnich banów na forum",
        "description" => "Dodaje tabele 5 ostatnich banów pobieranych z amxbansa na forum. Plugin specjalnie napisany przez Poftorka dla PaintBallMod.com",
        "disporder" => "50",
        "isdefault" => "yes"
    );
    $db->insert_query("settinggroups", $lastBansOnForum_group_settings);
    $gid = $db->insert_id();

    $option_2 = array(
        "sid" => NULL,
        "name" => "lastBansOnForum_url",
        "title" => "Adres amxbansa",
        "description" => "Podaj adres amxbansa w postaci twojadomena.pl/katalogamxbansa (bez https:// i ukośnika na końcu)",
        "optionscode" => "text",
        "value" => "twojastrona.pl/amxbans",
        "isdefault" => "yes",
        "disporder" => "2",
        "gid" => intval($gid)
    );
    $db->insert_query("settings", $option_2);

    $templateTable = '
		<div class="serversboard stable last5BansTable">
			<div class="headline">
				<div class="left">5 ostatnich banów</div>
			</div>
			<div class="container">
				<div class="serversboard__titles">
					<div class="serversboard__title">Nick</div>
					<div class="serversboard__title">Powód bana</div>
					<div class="serversboard__title">Czas bana</div>
					<div class="serversboard__title">Szczegóły bana</div>
				</div>
				{$lastBansOnForumBody}
			</div>
		</div>';

	$templateBody = '
		<div class="serversboard__row" title="Data bana: {$banDate} | Admin: {$banningAdmin} | Wygasa: {$expires}">
			<div class="serversboard__things serversboard__row--name"><span
					class="serversboard__thing serversboard__thing--name-cs16">{$nick}</span></div>
			<div class="serversboard__things serversboard__row--ip"><span
					class="serversboard__thing serversboard__thing--name-csgo">{$reason}</span></div>
			<div class="serversboard__things serversboard__row--ip"><span
					class="serversboard__thing serversboard__thing--name-ts3">{$banTime}</span></div>
			<div class="serversboard__things serversboard__row--buttons"><span class="banInfo"><a target="_blank"
						class="last5BansTable__banned" href="{$bid}"></a></span></div>
		</div>
	';

    $insert_array = array(
        'title' => 'lastBansOnForumTable',
        'template' => $db->escape_string($templateTable),
        'sid' => '-1',
        'version' => '',
        'dateline' => time()
    );
    $db->insert_query('templates', $insert_array);

    $insert_array2 = array(
        'title' => 'lastBansOnForumBody',
        'template' => $db->escape_string($templateBody),
        'sid' => '-1',
        'version' => '',
        'dateline' => time()
    );
    $db->insert_query('templates', $insert_array2);

    rebuild_settings();
}

function lastBansOnForum_deactivate() {
    global $db, $mybb;

    $db->delete_query('settinggroups', 'name = "lastBansOnForum_option_category"');
    $db->delete_query('settings', 'name = "lastBansOnForum_url"');
    $db->delete_query("templates", "title = 'lastBansOnForumTable'");
    $db->delete_query("templates", "title = 'lastBansOnForumBody'");

    rebuild_settings();
}

function lastBansOnForum() {
    global $mybb, $lastBansOnForum, $lastBansOnForumBody, $templates;
        $db_host = "";
        $db_user = "";
        $db_pass = "";
        $db_db = "";

        $conn = new mysqli($db_host, $db_user, $db_pass, $db_db);
        $conn->set_charset("utf8");
        $query  = "SELECT player_nick, ban_reason, ban_length, bid, from_unixtime(ban_created) as ban_date, admin_nick, ban_created, from_unixtime(ban_created+ban_length*60) as ban_expires FROM `amx_bans` ORDER BY ban_created DESC LIMIT 5";
        $result = $conn->query($query);

        $banDate = '';
        $banningAdmin = '';
        $expires = '';
        $nick = '';
        $reason = '';
        $banTime = '';
        $bid = '';

        while ($row = $result->fetch_row()) {
            $banDate = $row[4];
            $banningAdmin = htmlspecialchars_uni($row[5]);
            $expires = $row[2] == 0 ? 'Nigdy' : ($row[2] == -1 ? 'odbanowany' : $row[7]);
            $nick = htmlspecialchars_uni($row[0]);
            $reason = htmlspecialchars_uni($row[1]);
            $banTime = ($row[2] >= 60 && $row[2] % 60 == 0) ? ($row[2] / 60 . 'h') : ($row[2] == 0 ? 'na zawsze' : ($row[2] == -1 ? 'odbanowany' : $row[2] . 'min'));
            $bid = 'https://'.htmlspecialchars_uni($mybb->settings['lastBansOnForum_url']).'/ban_list.php?bid=' . $row[3];

			eval('$lastBansOnForumBody  .= "' . $templates->get('lastBansOnForumBody') . '";');
        }
			$lastBansOnForum = 'lastBansOnForumTable';
			eval('$lastBansOnForum  = "' . $templates->get('lastBansOnForumTable') . '";');
        $conn->close();

}

?>