<?php
 $jsonStr = file_get_contents(base_path().('\userconfig.json'));
		$userconfig = json_decode($jsonStr,true);
return [
    'users_options' => [
        'usernameminlength'      => $userconfig['userconfig']['usernameminlength'],
        'usernamemaxlength'      => $userconfig['userconfig']['usernamemaxlength'],
        'maximumloginsameuser'   => $userconfig['userconfig']['maximumloginsameuser'],
        'maximumloginattempt'    => $userconfig['userconfig']['maximumloginattempt'],
        'loginattemptresettime'  => $userconfig['userconfig']['loginattemptresettime'],
        'loginsecuritycode'      => $userconfig['userconfig']['loginsecuritycode'],
        'sessiontime'            => $userconfig['userconfig']['sessiontime'],
        'passwordtype'           => $userconfig['userconfig']['passwordtype'],
        'passwordexpireduration' => $userconfig['userconfig']['passwordexpireduration'],
        'lastusedpassword'       => $userconfig['userconfig']['lastusedpassword'],
        'bgclass'       => $userconfig['userconfig']['bgclass'], 
    ]
];
