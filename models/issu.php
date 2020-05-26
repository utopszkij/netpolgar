<?php
require_once('./core/github-client/client/GitHubClient.php');

class IssuRecord {
    public $title;
    public $body;
    public $sender;
    public $email;
}

class IssuModel {
    /**
     * issu adatok ellenörzése tárolás előtt
     * @param IssuRecord $data {title, body, sender, email}
     * @return array hibaüzenetek vagy []
     */
    public function check(IssuRecord $data): array {
        $msgs = [];
        if ($data->title == '') {
            $msgs[] = 'ERROR_ISSU_TITLE_EMPTY';
        }
        if ($data->body == '') {
            $msgs[] = 'ERROR_ISSU_BODY_EMPTY';
        }
        return $msgs;
    }
    
    /**
     * issu adatok küldése a github -ba
     * @param IssuRecord $data {title, body, sender, email}
     * @return array hibaüzenetek vagy []
     */
    public function send(IssuRecord $data): array {
        $data->body .= "\n\n".$data->sender."\n".$data->email;
        $client = new GitHubClient();
        if (config('GITHUB_USER') != '') {
            $client->setCredentials(config('GITHUB_USER'), config('GITHUB_PSW'));
            $client->issues->createAnIssue(config('GITHUB_USER'), config('GITHUB_REPO'), $data->title, $data->body);
        }
        return [];
    }
} // class
?>
