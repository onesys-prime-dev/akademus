<?php

namespace Tests\Feature;

use App\Controllers\AdmissionOfficer\ExamScheduleController;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\HTTP\UserAgent;
use CodeIgniter\Test\ControllerTestTrait;
use Config\App;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\DatabaseTestCase;

/**
 * Server-side validation of exam schedule create/update (officer area).
 *
 * @internal
 */
final class ExamScheduleValidationTest extends DatabaseTestCase
{
    use ControllerTestTrait;

    private function post(string $method, array $data, ...$params): array
    {
        $request = new IncomingRequest(config(App::class), new SiteURI(config(App::class)), null, new UserAgent());
        $request->setMethod('post')->setGlobal('post', $data);

        $result = $this->withRequest($request)->controller(ExamScheduleController::class)->execute($method, ...$params);

        return json_decode((string) $result->response()->getBody(), true);
    }

    private static function valid(array $override = []): array
    {
        return $override + [
            'exam_date'  => date('Y-m-d', strtotime('+30 days')),
            'start_time' => '09:00',
            'end_time'   => '11:00',
            'location'   => 'Room 101',
            'max_slots'  => '30',
        ];
    }

    #[DataProvider('provideInvalidScheduleIsRejected')]
    public function testInvalidScheduleIsRejected(array $data): void
    {
        $before = $this->db->table('exam_schedules')->countAllResults();

        $json = $this->post('create', $data);

        $this->assertFalse($json['success']);
        $this->assertNotEmpty($json['message']);
        $this->assertSame($before, $this->db->table('exam_schedules')->countAllResults(), 'Nothing may be inserted.');
    }

    /**
     * @return iterable<string, array{array<string, string>}>
     */
    public static function provideInvalidScheduleIsRejected(): iterable
    {
        yield 'past date' => [self::valid(['exam_date' => '2020-01-01'])];

        yield 'invalid date' => [self::valid(['exam_date' => '2026-02-31'])];

        yield 'end before start' => [self::valid(['start_time' => '10:00', 'end_time' => '09:00'])];

        yield 'end equals start' => [self::valid(['start_time' => '10:00', 'end_time' => '10:00'])];

        yield 'capacity 0' => [self::valid(['max_slots' => '0'])];

        yield 'capacity negative' => [self::valid(['max_slots' => '-5'])];

        yield 'missing location' => [self::valid(['location' => ''])];

        yield 'bad time' => [self::valid(['start_time' => '25:00'])];
    }

    public function testValidScheduleIsCreatedAndCanBeUpdated(): void
    {
        $json = $this->post('create', self::valid());
        $this->assertTrue($json['success'], json_encode($json));
        $this->seeInDatabase('exam_schedules', ['exam_schedule_id' => $json['id'], 'location' => 'Room 101']);

        $updated = $this->post('update', self::valid(['location' => 'Room 202']), $json['id']);
        $this->assertTrue($updated['success'], json_encode($updated));
        $this->seeInDatabase('exam_schedules', ['exam_schedule_id' => $json['id'], 'location' => 'Room 202']);

        $rejected = $this->post('update', self::valid(['end_time' => '08:00']), $json['id']);
        $this->assertFalse($rejected['success']);
    }

    public function testExistingPastDateCanBeKeptOnUpdate(): void
    {
        $this->db->table('exam_schedules')->insert(self::valid(['exam_date' => '2020-01-01']) + ['booked_slots' => 0, 'status' => 'open']);
        $id = $this->db->insertID();

        $this->assertTrue($this->post('update', self::valid(['exam_date' => '2020-01-01', 'location' => 'Hall']), $id)['success']);
        $this->assertFalse($this->post('update', self::valid(['exam_date' => '2020-01-02']), $id)['success']);
    }
}
