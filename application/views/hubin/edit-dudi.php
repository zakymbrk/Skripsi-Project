<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view') ?>"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view/data-dudi') ?>">Data DUDI</a></li>
        <li class="breadcrumb-item active">Edit DUDI</li>
    </ol>
</nav>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-building me-2"></i>Edit DUDI</h2>
</div>

<?php if(!empty($this->session->flashdata('message'))){ ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo $this->session->flashdata('message'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php } ?>

<?php if(!empty($this->session->flashdata('error_message'))){ ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?php echo $this->session->flashdata('error_message'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php } ?>

<!-- Card -->
<div class="card fade-in">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-edit me-2"></i>Edit Data DUDI
        </h5>
    </div>
    <div class="card-body">
        <form action="<?php echo site_url('hubin/update_dudi') ?>" method="post">
            <input type="hidden" name="dudi_code" value="<?php echo $dudi->dudi_code ?>">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="dudi_nama" class="form-label">Nama DUDI <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="dudi_nama" name="dudi_nama" 
                               value="<?php echo set_value('dudi_nama', $dudi->dudi_nama) ?>" required>
                        <?php echo form_error('dudi_nama', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="dudi_alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="dudi_alamat" name="dudi_alamat" rows="3" required><?php echo set_value('dudi_alamat', $dudi->dudi_alamat) ?></textarea>
                        <?php echo form_error('dudi_alamat', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="dudi_telepon" class="form-label">Telepon</label>
                        <input type="text" class="form-control" id="dudi_telepon" name="dudi_telepon" 
                               value="<?php echo set_value('dudi_telepon', $dudi->dudi_telepon ?? '') ?>">
                        <?php echo form_error('dudi_telepon', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="dudi_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="dudi_email" name="dudi_email" 
                               value="<?php echo set_value('dudi_email', $dudi->dudi_email ?? '') ?>">
                        <?php echo form_error('dudi_email', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="dudi_pic" class="form-label">Penanggung Jawab (PIC)</label>
                        <input type="text" class="form-control" id="dudi_pic" name="dudi_pic" 
                               value="<?php echo set_value('dudi_pic', $dudi->dudi_pic ?? '') ?>">
                        <?php echo form_error('dudi_pic', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="dudi_nip_pic" class="form-label">NIP PIC</label>
                        <input type="text" class="form-control" id="dudi_nip_pic" name="dudi_nip_pic" 
                               value="<?php echo set_value('dudi_nip_pic', $dudi->dudi_nip_pic ?? '') ?>">
                        <?php echo form_error('dudi_nip_pic', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="dudi_instruktur" class="form-label">Instruktur</label>
                        <input type="text" class="form-control" id="dudi_instruktur" name="dudi_instruktur" 
                               value="<?php echo set_value('dudi_instruktur', $dudi->dudi_instruktur ?? '') ?>">
                        <?php echo form_error('dudi_instruktur', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="dudi_nip_instruktur" class="form-label">NIP Instruktur</label>
                        <input type="text" class="form-control" id="dudi_nip_instruktur" name="dudi_nip_instruktur" 
                               value="<?php echo set_value('dudi_nip_instruktur', $dudi->dudi_nip_instruktur ?? '') ?>">
                        <?php echo form_error('dudi_nip_instruktur', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="is_mitra" class="form-label">Status Mitra</label>
                        <select class="form-select" id="is_mitra" name="is_mitra">
                            <option value="1" <?php echo set_value('is_mitra', $dudi->is_mitra ?? '') == '1' ? 'selected' : ''; ?>>Mitra</option>
                            <option value="0" <?php echo set_value('is_mitra', $dudi->is_mitra ?? '') == '0' ? 'selected' : ''; ?>>Non-Mitra</option>
                        </select>
                        <?php echo form_error('is_mitra', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>
            </div>

            <!-- Konfigurasi Geolocation & Radius Presensi (Rule-Based Validation) -->
            <div class="card bg-light border-0 rounded-4 p-4 mt-3 mb-2">
                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-map-marked-alt text-primary me-2"></i>Konfigurasi Titik Lokasi & Radius Presensi (GPS)</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="latitude" class="form-label small fw-semibold">Latitude DUDI</label>
                        <input type="text" class="form-control form-control-sm" id="latitude" name="latitude" 
                               value="<?php echo set_value('latitude', $dudi->latitude ?? '') ?>" placeholder="Contoh: -7.032145">
                        <div class="form-text text-muted" style="font-size: 0.75rem;">Titik lintang lokasi kantor DUDI.</div>
                        <?php echo form_error('latitude', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    <div class="col-md-4">
                        <label for="longitude" class="form-label small fw-semibold">Longitude DUDI</label>
                        <input type="text" class="form-control form-control-sm" id="longitude" name="longitude" 
                               value="<?php echo set_value('longitude', $dudi->longitude ?? '') ?>" placeholder="Contoh: 107.712345">
                        <div class="form-text text-muted" style="font-size: 0.75rem;">Titik bujur lokasi kantor DUDI.</div>
                        <?php echo form_error('longitude', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    <?php
                        // Load Google Maps API key from config
                        $google_maps_key = $this->config->item('google_maps_api_key');
                    ?>
                    <div class="col-md-4">
                        <label for="radius" class="form-label small fw-semibold">Radius Toleransi (Meter) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-sm" id="radius" name="radius" 
                               value="<?php echo set_value('radius', isset($dudi->radius) ? $dudi->radius : 100) ?>" min="10" max="5000" required>
                        <div class="form-text text-muted" style="font-size: 0.75rem;">Batas jarak maksimal presensi (default: 100 meter).</div>
                        <?php echo form_error('radius', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    <div class="col-12 mt-3">
                        <div id="map" style="height: 400px; width: 100%;"></div>
                    </div>
                    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_maps_key; ?>"></script>
                    <script>
                        function initMap() {
                            var latInput = document.getElementById('latitude');
                            var lngInput = document.getElementById('longitude');
                            var radiusInput = document.getElementById('radius');
                            var lat = parseFloat(latInput.value) || -7.032145; // default lat
                            var lng = parseFloat(lngInput.value) || 107.712345; // default lng
                            var radius = parseInt(radiusInput.value) || 100;
                            var map = new google.maps.Map(document.getElementById('map'), {
                                center: {lat: lat, lng: lng},
                                zoom: 15
                            });
                            var marker = new google.maps.Marker({
                                position: {lat: lat, lng: lng},
                                map: map,
                                draggable: true
                            });
                            var circle = new google.maps.Circle({
                                map: map,
                                radius: radius,
                                fillColor: '#AA0000',
                                strokeColor: '#AA0000',
                                editable: true
                            });
                            circle.bindTo('center', marker, 'position');
                            // Update inputs when marker moved
                            marker.addListener('dragend', function(event) {
                                latInput.value = event.latLng.lat().toFixed(6);
                                lngInput.value = event.latLng.lng().toFixed(6);
                            });
                            // Update radius when circle radius changed
                            circle.addListener('radius_changed', function() {
                                radiusInput.value = Math.round(circle.getRadius());
                            });
                            // If user manually changes radius input, update circle radius
                            radiusInput.addEventListener('change', function(){
                                var r = parseInt(this.value) || 100;
                                circle.setRadius(r);
                            });
                        }
                        google.maps.event.addDomListener(window, 'load', initMap);
                    </script>
                </div>
            </div>
            
            <hr>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Data
                </button>
            </div>
        </form>
    </div>
</div>