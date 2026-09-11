<?php $__env->startSection('title', 'Nyumbani - Pata Gesi Karibu Yako'); ?>

<?php $__env->startSection('content'); ?>

<section class="hero-section text-center">
    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3 animate-fade-in">
                    <i class="bi bi-fire"></i> GasPOA
                </h1>
                <p class="lead fs-3 mb-4 animate-fade-in-up">
                    Gesi yako, papo kwa hapo kutoka kwa wauzaji wa karibu yako.
                </p>
                <div class="d-flex justify-content-center gap-3 flex-wrap animate-fade-in-up">
                    <a href="<?php echo e(route('register')); ?>" class="btn btn-light btn-lg px-4 shadow-sm">
                        <i class="bi bi-person-plus"></i> Jisajili Sasa
                    </a>
                    <a href="#how-it-works" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-info-circle"></i> Jinsi Inavyofanya Kazi
                    </a>
                </div>
            </div>
        </div>
        
        <div class="hero-shape"></div>
    </div>
</section>


<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold display-6">Unahitaji Huduma Gani Leo?</h2>
            <p class="text-muted lead">Chagua moja ya huduma zetu hapa chini</p>
        </div>
        
        <div class="row g-4 justify-content-center">
            
            <div class="col-12 col-md-6 col-lg-5">
                <div class="card service-card h-100 border-0 shadow-hover">
                    <div class="card-body p-4 p-lg-5 text-center">
                        <div class="icon-wrapper bg-success bg-opacity-10 text-success mx-auto mb-4">
                            <i class="bi bi-arrow-repeat display-4"></i>
                        </div>
                        <h3 class="fw-bold mb-3">Kubadilisha Mtungi (Refill)</h3>
                        <p class="text-muted mb-4">
                            Unamaliza gesi? Lete mtungi wako tupu, ubadilishe na uliojaa. 
                            Tunakutafutia wauzaji wa karibu yako.
                        </p>
                        <a href="<?php echo e(route('consumer.order.create', ['type' => 'refill'])); ?>" 
                           class="btn btn-success btn-lg w-100 rounded-pill">
                            Chagua Refill <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            
            <div class="col-12 col-md-6 col-lg-5">
                <div class="card service-card h-100 border-0 shadow-hover">
                    <div class="card-body p-4 p-lg-5 text-center">
                        <div class="icon-wrapper bg-warning bg-opacity-10 text-warning mx-auto mb-4">
                            <i class="bi bi-cart4 display-4"></i>
                        </div>
                        <h3 class="fw-bold mb-3">Kununua Mtungi Mpya</h3>
                        <p class="text-muted mb-4">
                            Unahitaji mtungi mpya kabisa? Tunakuletea mtungi mpya na wa uhakika 
                            kutoka kwa maduka yaliyothibitishwa.
                        </p>
                        <a href="<?php echo e(route('consumer.order.create', ['type' => 'new'])); ?>" 
                           class="btn btn-warning btn-lg w-100 rounded-pill">
                            Chagua Mtungi Mpya <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-5 ussd-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8 mx-auto">
                <div class="ussd-card text-center p-5">
                    <div class="display-1 mb-3 text-white">
                        <i class="bi bi-phone"></i>
                    </div>
                    <h3 class="fw-bold text-white">Huna Simu Janja au Intaneti?</h3>
                    <p class="lead text-white-50 mt-3">
                        Piga <strong class="ussd-code">*150*99#</strong> kwenye simu yako yoyote
                    </p>
                    <p class="text-white-50">
                        Fuata maelekezo rahisi na upate gesi kwa urahisi bila kutumia intaneti. 
                        Huduma ya USSD inapatikana saa 24, siku 7 kwa wiki.
                    </p>
                    <div class="mt-4">
                        <span class="badge bg-success bg-opacity-75 p-2 px-3 me-2"><i class="bi bi-check-lg"></i> Haraka</span>
                        <span class="badge bg-info bg-opacity-75 p-2 px-3 me-2"><i class="bi bi-check-lg"></i> Rahisi</span>
                        <span class="badge bg-secondary bg-opacity-75 p-2 px-3"><i class="bi bi-check-lg"></i> Salama</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section id="how-it-works" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold display-6">Jinsi GasPOA Inavyofanya Kazi</h2>
            <p class="text-muted lead">Hatua tatu rahisi kupata gesi yako</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="step-card text-center p-4 bg-white rounded-4 shadow-sm h-100">
                    <div class="step-number bg-primary bg-opacity-10 text-primary mx-auto mb-3">1</div>
                    <h5 class="fw-bold">Chagua Huduma</h5>
                    <p class="text-muted">Chagua kama unataka Kubadilisha Mtungi au Mtungi Mpya.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card text-center p-4 bg-white rounded-4 shadow-sm h-100">
                    <div class="step-number bg-primary bg-opacity-10 text-primary mx-auto mb-3">2</div>
                    <h5 class="fw-bold">Tunakutafutia Muuzaji</h5>
                    <p class="text-muted">Tunakutafutia wauzaji wa karibu yako wenye bidhaa unayohitaji.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card text-center p-4 bg-white rounded-4 shadow-sm h-100">
                    <div class="step-number bg-primary bg-opacity-10 text-primary mx-auto mb-3">3</div>
                    <h5 class="fw-bold">Pokea na Lipa</h5>
                    <p class="text-muted">Gesi inakufikia, lipa kwa Pesa Taslimu au M-Pesa/TigoPesa/Airtel Money.</p>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-5 cta-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h3 class="fw-bold text-white">Unamiliki Duka la Gesi au Ghala?</h3>
                <p class="lead text-white-50">Jiunge na GasPOA Market kama Muuzaji na upate wateja zaidi.</p>
                <a href="<?php echo e(route('register')); ?>?type=retailer" class="btn btn-light btn-lg px-5 mt-3 rounded-pill shadow">
                    <i class="bi bi-shop"></i> Jisajili Kama Mfanyabiashara
                </a>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\home\index.blade.php ENDPATH**/ ?>