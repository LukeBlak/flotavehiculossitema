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
    it('ningun rol tiene acceso hasta implementar', function () {
        $log = FuelLog::factory()->make();
        expect($this->policy->viewAny(gerente()))->toBeFalse()
            ->and($this->policy->create(motorista()))->toBeFalse()
            ->and($this->policy->delete(gerente(), $log))->toBeFalse();
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
    it('motorista no puede ver incidente de otro motorista', function () {
        $driver1 = motorista();
        $driver2 = motorista();
        $incident = Incident::factory()->make(['reported_by' => $driver2->id]);
        expect($this->policy->view($driver1, $incident))->toBeFalse();
    });
    it('supervisor no puede crear incidentes', function () {
        expect($this->policy->create(supervisor()))->toBeFalse();
    });
    it('gerente no puede crear incidentes', function () {
        expect($this->policy->create(gerente()))->toBeFalse();
    });
});

describe('MaintenanceLogPolicy - restricciones por rol', function () {
    beforeEach(fn() => $this->policy = new MaintenanceLogPolicy());
    it('supervisor no puede editar orden aprobada', function () {
        $log = MaintenanceLog::factory()->make(['status' => 'approved']);
        expect($this->policy->update(supervisor(), $log))->toBeFalse();
    });
    it('gerente no puede editar ordenes', function () {
        $log = MaintenanceLog::factory()->make(['status' => 'pending']);
        expect($this->policy->update(gerente(), $log))->toBeFalse();
    });
    it('motorista no puede editar ordenes', function () {
        $log = MaintenanceLog::factory()->make(['status' => 'pending']);
        expect($this->policy->update(motorista(), $log))->toBeFalse();
    });
    it('motorista no puede crear ordenes de mantenimiento', function () {
        expect($this->policy->create(motorista()))->toBeFalse();
    });
});
