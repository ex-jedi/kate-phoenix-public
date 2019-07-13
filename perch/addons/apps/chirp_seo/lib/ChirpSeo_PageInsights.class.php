<?php

use PageSpeed\Insightsd;

class ChirpSeo_PageInsights extends ChirpSeo_Base
{
  private $api_key = "AIzaSyBIdzxrfFNWHukzGYVldaAM7jmNI01qeNU";
  private $url = false;

  function __construct($url)
  {
    $this->url = $url;
    $this->errorMessage = false;

    $this->red_score = 50;
    $this->amber_score = 70;

    try {
      $this->calculate_score();
    } catch (Exception $e) {
      $this->errorMessage = $e->getMessage();
    }
  }

  public function calculate_score() {
    $pageSpeed = new \PageSpeed\Insights\Service();

    $results = $pageSpeed->getResults($this->url);

    $this->score = $results["ruleGroups"]["SPEED"]["score"];
  }

  public function get_traffic_light() {
    if ($this->score <= $this->red_score) {
      return "red";
    } else if ($this->score <= $this->amber_score) {
      return "amber";
    }

    return "green";
  }

  public function get_title() {
    if ($this->score <= $this->red_score) {
      $title = "Get to work!";
    } else if ($this->score <= $this->amber_score) {
      $title = "Good job!";
    } else {
      $title = "Nailed it!";
    }

    return $title;
  }

  public function get_description() {
    $description = "";

    $description = PerchLang::get("Your Google Insights pagescore is %s, view feedback %shere%s.", $this->score, '<a href="https://developers.google.com/speed/pagespeed/insights/?url=' . $this->url . '&tab=desktop" target="_blank">', '</a>');

    return $description;
  }
}
