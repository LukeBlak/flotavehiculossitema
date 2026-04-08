<?php
use App\Policies\TripPolicy;
use App\Policies\IncidentPolicy;
use App\Policies\VehiclePolicy;
use App\Policies\VehicleTypePolicy;
use App\Policies\FuelLogPolicy;
use App\Policies\MaintenanceLogPolicy;
use App\Models\Trip;
use App\Models\Incident;
use App\Models\Vehicle;
use App\Models\MaintenanceLog;
use App\Models\FuelLog;

// ============================================================
// CRITERIO 4 - Tests sobre el codigo real del repositorio
// Prueba el comportamiento ACTUAL de las Policies
// ============================================================

describe('VehiclePolicy - stub sin implementar', function () {
    beforeEach(fn() => $this->policy = new VehiclePolicy());
    it('ningun rol puede ver vehiculos hasta implementar', function () {
        expect($this->policy->viewAny(gerente()))->toBeFalse()
            ->and($this->policy->viewAny(supervisor()))->toBeFalse()
            ->and($this->policy->viewAny(motorista()))->toBeFalse();
    });
    it('ningun rol puede crear vehiculos hasta implementar', function () {
        expect($this->policy->create(gerente()))->toBeFalse()
            ->and($this->policy->create(supervisor()))->toBeFalse()
            ->and($this->policy->create(motorista()))->toBeFalse();
    });
    it('motorista no puede eliminar vehiculos', function () {
        expect($this->policy->delete(motorista(), Vehicle::factory()->make()))->toBeFalse();
    });
});

describe('VehicleTypePolicy - stub sin implementar', function () {
    beforeEach(fn() => $this->policy = new VehicleTypePolicy());
    it('ningun rol tiene acceso hasta implementar', function () {
        expect($this->policy->viewAny(gerente()))->toBeFalse()
            ->and($this->policy->create(supervisor()))->toBeFalse()
            ->and($this->policy->create(motorista()))->toBeFalse();
    });
});

describe('FuelLogPolicy - stub sin implementar', function () {
    beforeEach(fn() => $this->policy = new FuelLogPolicy());
    it('gerente y supervisor pueden ver registros, motorista solo los suyos', function () {
        $driver = motorista();
        $ownLog = FuelLog::factory()->make(['user_id' => $driver->id]);
        $otherLog = FuelLog::factory()->make(['user_id' => gerente()->id]);

        expect($this->policy->viewAny(gerente()))->toBeTrue()
            ->and($this->policy->viewAny(supervisor()))->toBeTrue()
            ->and($this->policy->viewAny($driver))->toBeFalse()
            ->and($this->policy->view($driver, $ownLog))->toBeTrue()
            ->and($this->policy->view($driver, $otherLog))->toBeFalse()
            ->and($this->policy->create($driver))->toBeTrue()
            ->and($this->policy->create(gerente()))->toBeFalse();
    });
});

describe('TripPolicy - restricciones por rol', function () {
    beforeEach(fn() => $this->policy = new TripPolicy());
    it('motorista no puede ver viaje de otro conductor', function () {
        $driver1 = motorista();
        $driver2 = motorista();
        $trip = Trip::factory()->make(['driver_id' => $driver2->id]);
        expect($this->policy->view($driver1, $trip))->toBeFalse();
    });
    it('motorista no puede ver viaje con driver_id null', function () {
        expect($this->policy->view(motorista(), Trip::factory()->make(['driver_id' => null])))->toBeFalse();
    });
    it('gerente no puede crear viajes', function () {
        expect($this->policy->create(gerente()))->toBeFalse();
    });
    it('motorista no puede crear viajes', function () {
        expect($this->policy->create(motorista()))->toBeFalse();
    });
});

describe('IncidentPolicy - restricciones por rol', function () {
    beforeEach(fn() => $this->policy = new IncidentPolicy());
    it('motorista puede ver solo sus incidentes y reportarlos', function () {
        $driver1 = motorista();
        $driver2 = motorista();
        $ownIncident = Incident::factory()->make(['user_id' => $driver1->id]);
        $otherIncident = Incident::factory()->make(['user_id' => $driver2->id]);

        expect($this->policy->view($driver1, $ownIncident))->toBeTrue()
            ->and($this->policy->view($driver1, $otherIncident))->toBeFalse()
            ->and($this->policy->create($driver1))->toBeTrue()
            ->and($this->policy->create(supervisor()))->toBeFalse()
            ->and($this->policy->create(gerente()))->toBeFalse();
    });
});

describe('MaintenanceLogPolicy - restricciones por rol', function () {
    beforeEach(fn() => $this->policy = new MaintenanceLogPolicy());
    it('supervisor puede crear y editar solo ordenes pendientes', function () {
        $log = MaintenanceLog::factory()->make(['status' => 'pending']);
        $approvedLog = MaintenanceLog::factory()->make(['status' => 'approved']);

        expect($this->policy->viewAny(supervisor()))->toBeTrue()
            ->and($this->policy->create(supervisor()))->toBeTrue()
            ->and($this->policy->update(supervisor(), $log))->toBeTrue()
            ->and($this->policy->update(supervisor(), $approvedLog))->toBeFalse()
            ->and($this->policy->approve(supervisor(), $log))->toBeTrue()
            ->and($this->policy->approve(supervisor(), MaintenanceLog::factory()->make(['cost' => 250])))->toBeFalse()
            ->and($this->policy->approve(gerente(), MaintenanceLog::factory()->make(['cost' => 250])))->toBeTrue();
    });
});
