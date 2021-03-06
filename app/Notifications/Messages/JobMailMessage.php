<?php namespace JobApis\JobsToMail\Notifications\Messages;

use Illuminate\Notifications\Messages\MailMessage;
use JobApis\Jobs\Client\Job;

class JobMailMessage extends MailMessage
{
    public $jobListings = [];

    /**
     * Add a Job listing to the notification
     *
     * @param  Job $job
     *
     * @return $this
     */
    public function listing(Job $job)
    {
        $line = "";
        $line .= $this->getTitle($job->getTitle());
        $line .= $this->getCompany($job->getCompanyName());
        $line .= $this->getLocation($job->getLocation());
        $line .= ".";
        $this->jobListings[] = [
            'link' => $job->getUrl(),
            'text' => $line,
            'date' => $this->getDate($job->getDatePosted()),
        ];
        return $this;
    }

    /**
     * Get the data array for the mail message.
     *
     * @return array
     */
    public function data()
    {
        return array_merge(
            $this->toArray(),
            $this->viewData,
            ['jobListings' => $this->jobListings]
        );
    }

    private function getTitle($title)
    {
        return $title ?: null;
    }

    private function getLocation($location)
    {
        return $location ? " in {$location}" : null;
    }

    private function getCompany($company)
    {
        return $company ? " at {$company}" : null;
    }

    private function getDate($dateTime)
    {
        if (is_object($dateTime) && \DateTime::class == get_class($dateTime)) {
            return $dateTime->format('F j, Y');
        }
        return null;
    }
}
