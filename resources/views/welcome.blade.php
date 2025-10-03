@extends('layouts.template')
@section('content')
    <style>
        h1 {
            font-family: 'Volkhov', DM Serif Display;
            font-size: 85px;
            color: #456bdc;
            /* Warna teks */
            text-align: left;
            letter-spacing: 2px;
            margin-bottom: 1px;
            /* Jarak antar elemen */
        }

        h3 {
            font-family: 'Volkhov', DM Serif Display;
            font-size: 55px;
            color: #456bdc;
            /* Warna teks */
            text-align: center;
            letter-spacing: 2px;
            margin-bottom: 1px;
            /* Jarak antar elemen */
        }

        p {
            font-family: DM Serif Display;
            font-size: 20px;
        }
    </style>

    <main class="main" id="top">
        <section style="padding-top: 7rem;">
            <!-- <div class="bg-holder" style="background-image: url('{{ asset('asset/img/hero/hero-bg.svg') }}');"></div> -->
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-5 col-lg-6 order-0 order-md-1 text-end"><img class="pt-7 pt-md-0 hero-img"
                            src="{{ asset('asset/img/hero/hero-img.png') }}" alt="hero-header" /></div>
                    <div class="col-md-7 col-lg-6 text-md-start text-center py-6">
                        <h4 class="fw-bold text-primary mb-3">Learning Management System</h4>
                        <h1>Ruang Anagata</h1>
                        <b>
                            <p class="mb-4 fw-medium-primary">Dokumentasi berbagai keperluan pembelajaran! <br>
                                Modul, Pembelajaran, dan berbagai fungsi untuk kebutuhan pembelajaran.</p>
                        </b>
                        <div class="w-100 d-block d-md-none"></div>
                    </div>
                </div>
            </div>
            </div>
            </div>
            </div>
            </div>
        </section>
    </main>
@endsection
