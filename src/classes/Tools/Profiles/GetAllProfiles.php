<?php
namespace dhope0000\LXDClient\Tools\Profiles;

use dhope0000\LXDClient\Model\Client\LxdClient;
use dhope0000\LXDClient\Tools\Hosts\GetClustersAndStandaloneHosts;

class GetAllProfiles
{
    public function __construct(LxdClient $lxdClient, GetClustersAndStandaloneHosts $getClustersAndStandaloneHosts)
    {
        $this->client = $lxdClient;
        $this->getClustersAndStandaloneHosts = $getClustersAndStandaloneHosts;
    }

    private function getProfiles($host)
    {
        $indent = is_null($host["alias"]) ? $host["urlAndPort"] : $host["alias"];

        if (isset($host["online"]) && $host["online"] != true) {
            return [
                "online"=>false,
                "hostId"=>$host["hostId"]
            ];
        }

        $client = $this->client->getANewClient($host["hostId"]);

        $profiles = $client->profiles->all();

        return [
            "hostAlias"=>$indent,
            "online"=>true,
            "hostId"=>$host["hostId"],
            "profiles"=>$this->getProfileDetails($client, $profiles)
        ];
    }


    public function getAllProfiles()
    {
        $clustersAndHosts = $this->getClustersAndStandaloneHosts->get();

        foreach ($clustersAndHosts["clusters"] as $clusterIndex => $cluster) {
            foreach ($cluster["members"] as $hostIndex => $host) {
                $profiles = $this->getProfiles($host);
                $clustersAndHosts["clusters"][$clusterIndex]["members"][$hostIndex] = $profiles;
            }
        }

        foreach ($clustersAndHosts["standalone"]["members"] as $index => $host) {
            $clustersAndHosts["standalone"]["members"][$index] =  $this->getProfiles($host);
        }

        return $clustersAndHosts;
    }

    public function getHostProfilesWithDetails(int $hostId)
    {
        $client = $this->client->getANewClient($hostId);
        $profiles = $client->profiles->all();
        return $this->getProfileDetails($client, $profiles);
    }

    public function getProfileDetails($client, $profiles)
    {
        $details = array();
        foreach ($profiles as $profile) {
            $state = $client->profiles->info($profile);
            $details[$profile] = ["details"=>$state];
        }
        return $details;
    }
}
