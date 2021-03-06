<?php namespace JobApis\JobsToMail\Tests\Unit\Jobs;

use Illuminate\Support\Facades\Log;
use JobApis\Jobs\Client\Collection;
use JobApis\JobsToMail\Tests\TestCase;
use Mockery as m;
use JobApis\JobsToMail\Jobs\SearchAndNotifyUser;

class SearchAndNotifyUserTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->user = m::mock('JobApis\JobsToMail\Models\User');
        $this->search = m::mock('JobApis\JobsToMail\Models\Search');
        $this->job = new SearchAndNotifyUser($this->search);
    }

    public function testItCanHandleWhenJobsFoundFromOneProvider()
    {
        $client = m::mock('JobApis\Jobs\Client\JobsMulti');
        $keyword = uniqid();
        $location = uniqid();

        $jobs = [
            'Provider1' => m::mock(Collection::class),
        ];
        $jobsArray = $this->getJobsArray();

        $this->search->shouldReceive('getAttribute')
            ->with('keyword')
            ->once()
            ->andReturn($keyword);
        $client->shouldReceive('setKeyword')
            ->with($keyword)
            ->once()
            ->andReturnSelf();
        $this->search->shouldReceive('getAttribute')
            ->with('location')
            ->once()
            ->andReturn($location);
        $client->shouldReceive('setLocation')
            ->with($location)
            ->once()
            ->andReturnSelf();
        $client->shouldReceive('setPage')
            ->with(1, 10)
            ->once()
            ->andReturnSelf();
        $client->shouldReceive('getAllJobs')
            ->once()
            ->andReturn($jobs);
        $jobs['Provider1']->shouldReceive('getErrors')
            ->once()
            ->andReturn([]);
        $jobs['Provider1']->shouldReceive('all')
            ->once()
            ->andReturn($jobsArray);
        $this->search->shouldReceive('getAttribute')
            ->with('user')
            ->once()
            ->andReturn($this->user);
        $this->user->shouldReceive('notify')
            ->once()
            ->andReturnSelf();

        $results = $this->job->handle($client);

        $this->assertEquals($jobsArray, $results);
    }

    public function testItCanHandleWhenJobsFoundFromMultipleProviders()
    {
        $client = m::mock('JobApis\Jobs\Client\JobsMulti');
        $keyword = uniqid();
        $location = uniqid();

        $jobs = [
            'Provider1' => m::mock(Collection::class),
            'Provider2' => m::mock(Collection::class),
            'Provider3' => m::mock(Collection::class),
        ];
        $jobsArray = $this->getJobsArray();

        $this->search->shouldReceive('getAttribute')
            ->with('keyword')
            ->once()
            ->andReturn($keyword);
        $client->shouldReceive('setKeyword')
            ->with($keyword)
            ->once()
            ->andReturnSelf();
        $this->search->shouldReceive('getAttribute')
            ->with('location')
            ->once()
            ->andReturn($location);
        $client->shouldReceive('setLocation')
            ->with($location)
            ->once()
            ->andReturnSelf();
        $client->shouldReceive('setPage')
            ->with(1, 10)
            ->once()
            ->andReturnSelf();
        $client->shouldReceive('getAllJobs')
            ->once()
            ->andReturn($jobs);
        $jobs['Provider1']->shouldReceive('getErrors')
            ->once()
            ->andReturn([]);
        $jobs['Provider1']->shouldReceive('all')
            ->once()
            ->andReturn($jobsArray);
        $jobs['Provider2']->shouldReceive('getErrors')
            ->once()
            ->andReturn([]);
        $jobs['Provider2']->shouldReceive('all')
            ->once()
            ->andReturn($jobsArray);
        $jobs['Provider3']->shouldReceive('getErrors')
            ->once()
            ->andReturn([]);
        $jobs['Provider3']->shouldReceive('all')
            ->once()
            ->andReturn($jobsArray);
        $this->search->shouldReceive('getAttribute')
            ->with('user')
            ->once()
            ->andReturn($this->user);
        $this->user->shouldReceive('notify')
            ->once()
            ->andReturnSelf();

        $results = $this->job->handle($client);

        $this->assertEquals(count($jobsArray)*count($jobs), count($results));
    }

    public function testItCanHandleWhenMoreThanMaxJobsFound()
    {
        $client = m::mock('JobApis\Jobs\Client\JobsMulti');
        $keyword = uniqid();
        $location = uniqid();

        $jobs = [
            'Provider1' => m::mock(Collection::class),
            'Provider2' => m::mock(Collection::class),
            'Provider3' => m::mock(Collection::class),
            'Provider4' => m::mock(Collection::class),
            'Provider5' => m::mock(Collection::class),
        ];
        $jobsArray = $this->getJobsArray(20);

        $this->search->shouldReceive('getAttribute')
            ->with('keyword')
            ->once()
            ->andReturn($keyword);
        $client->shouldReceive('setKeyword')
            ->with($keyword)
            ->once()
            ->andReturnSelf();
        $this->search->shouldReceive('getAttribute')
            ->with('location')
            ->once()
            ->andReturn($location);
        $client->shouldReceive('setLocation')
            ->with($location)
            ->once()
            ->andReturnSelf();
        $client->shouldReceive('setPage')
            ->with(1, 10)
            ->once()
            ->andReturnSelf();
        $client->shouldReceive('getAllJobs')
            ->once()
            ->andReturn($jobs);
        $jobs['Provider1']->shouldReceive('getErrors')
            ->once()
            ->andReturn([]);
        $jobs['Provider1']->shouldReceive('all')
            ->once()
            ->andReturn($jobsArray);
        $jobs['Provider2']->shouldReceive('getErrors')
            ->once()
            ->andReturn([]);
        $jobs['Provider2']->shouldReceive('all')
            ->once()
            ->andReturn($jobsArray);
        $jobs['Provider3']->shouldReceive('getErrors')
            ->once()
            ->andReturn([]);
        $jobs['Provider3']->shouldReceive('all')
            ->once()
            ->andReturn($jobsArray);
        $jobs['Provider4']->shouldReceive('getErrors')
            ->once()
            ->andReturn([]);
        $jobs['Provider4']->shouldReceive('all')
            ->once()
            ->andReturn($jobsArray);
        $jobs['Provider5']->shouldReceive('getErrors')
            ->once()
            ->andReturn([]);
        $jobs['Provider5']->shouldReceive('all')
            ->once()
            ->andReturn($jobsArray);
        $this->search->shouldReceive('getAttribute')
            ->with('user')
            ->once()
            ->andReturn($this->user);
        $this->user->shouldReceive('notify')
            ->once()
            ->andReturnSelf();

        $results = $this->job->handle($client);

        $this->assertEquals(50, count($results));
    }

    public function testItCanHandleWhenOldJobsFound()
    {
        $client = m::mock('JobApis\Jobs\Client\JobsMulti');
        $keyword = uniqid();
        $location = uniqid();

        $jobs = [
            'Provider1' => m::mock(Collection::class),
        ];
        $jobsArray = $this->getJobsArray();
        $jobsArray[0]->datePosted = $this->faker->dateTime('-1 year');

        $this->search->shouldReceive('getAttribute')
            ->with('keyword')
            ->once()
            ->andReturn($keyword);
        $client->shouldReceive('setKeyword')
            ->with($keyword)
            ->once()
            ->andReturnSelf();
        $this->search->shouldReceive('getAttribute')
            ->with('location')
            ->once()
            ->andReturn($location);
        $client->shouldReceive('setLocation')
            ->with($location)
            ->once()
            ->andReturnSelf();
        $client->shouldReceive('setPage')
            ->with(1, 10)
            ->once()
            ->andReturnSelf();
        $client->shouldReceive('getAllJobs')
            ->once()
            ->andReturn($jobs);
        $jobs['Provider1']->shouldReceive('getErrors')
            ->once()
            ->andReturn([]);
        $jobs['Provider1']->shouldReceive('all')
            ->once()
            ->andReturn($jobsArray);
        $this->search->shouldReceive('getAttribute')
            ->with('user')
            ->once()
            ->andReturn($this->user);
        $this->user->shouldReceive('notify')
            ->once()
            ->andReturnSelf();

        $results = $this->job->handle($client);

        $this->assertEquals(1, count($results));
    }

    public function testItCanHandleWhenNoJobsFound()
    {
        $client = m::mock('JobApis\Jobs\Client\JobsMulti');
        $keyword = uniqid();
        $location = uniqid();

        $jobs = [
            'Provider1' => m::mock(Collection::class),
        ];
        $jobsArray = [];

        $this->search->shouldReceive('getAttribute')
            ->with('keyword')
            ->once()
            ->andReturn($keyword);
        $client->shouldReceive('setKeyword')
            ->with($keyword)
            ->once()
            ->andReturnSelf();
        $this->search->shouldReceive('getAttribute')
            ->with('location')
            ->once()
            ->andReturn($location);
        $client->shouldReceive('setLocation')
            ->with($location)
            ->once()
            ->andReturnSelf();
        $client->shouldReceive('setPage')
            ->with(1, 10)
            ->once()
            ->andReturnSelf();
        $client->shouldReceive('getAllJobs')
            ->once()
            ->andReturn($jobs);
        $jobs['Provider1']->shouldReceive('getErrors')
            ->once()
            ->andReturn([]);
        $jobs['Provider1']->shouldReceive('all')
            ->once()
            ->andReturn($jobsArray);
        $this->search->shouldReceive('getAttribute')
            ->with('id')
            ->once()
            ->andReturn(uniqid());
        Log::shouldReceive('info')
            ->once()
            ->andReturnSelf();

        $results = $this->job->handle($client);

        $this->assertEquals($jobsArray, $results);
    }

    public function testItCanHandleWhenErrorsExistOnJobCollection()
    {
        $client = m::mock('JobApis\Jobs\Client\JobsMulti');
        $keyword = uniqid();
        $location = uniqid();
        $error = $this->faker->sentence();

        $jobs = [
            'Provider1' => m::mock(Collection::class),
        ];
        $jobsArray = $this->getJobsArray();

        $this->search->shouldReceive('getAttribute')
            ->with('keyword')
            ->once()
            ->andReturn($keyword);
        $client->shouldReceive('setKeyword')
            ->with($keyword)
            ->once()
            ->andReturnSelf();
        $this->search->shouldReceive('getAttribute')
            ->with('location')
            ->once()
            ->andReturn($location);
        $client->shouldReceive('setLocation')
            ->with($location)
            ->once()
            ->andReturnSelf();
        $client->shouldReceive('setPage')
            ->with(1, 10)
            ->once()
            ->andReturnSelf();
        $client->shouldReceive('getAllJobs')
            ->once()
            ->andReturn($jobs);
        $jobs['Provider1']->shouldReceive('getErrors')
            ->twice()
            ->andReturn([$error]);
        Log::shouldReceive('error')
            ->once()
            ->with($error)
            ->andReturnSelf();
        $jobs['Provider1']->shouldReceive('all')
            ->once()
            ->andReturn($jobsArray);
        $this->search->shouldReceive('getAttribute')
            ->with('user')
            ->once()
            ->andReturn($this->user);
        $this->user->shouldReceive('notify')
            ->once()
            ->andReturnSelf();

        $results = $this->job->handle($client);

        $this->assertEquals($jobsArray, $results);
    }

    private function getJobsArray($number = 2)
    {
        $jobsArray = [];
        $count = 0;
        while ($count < $number) {
            $jobsArray[] = (object) [
                'title' => $this->faker->sentence(),
                'datePosted' => new \DateTime(),
            ];
            $count++;
        }
        return $jobsArray;
    }
}
