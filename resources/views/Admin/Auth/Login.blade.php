<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CA Customer Care | Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
        
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
</head>

<body style="background: rgb(10,113,171);">
    <div class="container mt-5" style="position: absolute;top: 30%;left: 50%;transform: translate(-50%, -30%)">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-6 col-sm-8">
                <h2 class="text-center mb-4" style="color: white">CA Customer Care</h2>
                <div class="card shadow-lg">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Sign In</h2>
                        <form action="{{ url('/login') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                {{-- <label for="email" class="form-label">Email address</label> --}}
                                <input type="email" name="email" id="form2Example1" placeholder="Enter your email address"
                                    class="form-control" value="{{ old('email') }}" />
                                @if (session()->has('msg-error-username'))
                                    <span class="text-danger">{{ session('msg-error-username') }}</span>
                                @endif

                            </div>
                            <div class="mb-1">
                                {{-- <label for="password" class="form-label">Password</label> --}}
                                <input type="password" id="form2Example2" name="password" class="form-control" placeholder="Password" />
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                @if (session()->has('msg-error-password'))
                                    <span class="text-danger">{{ session('msg-error-password') }}</span>
                                @endif
                            </div>
                            {{-- <span class="mb-3"> <a href="#" style="text-decoration: none">Forgot Password?</a></span> --}}

                            <div class="mt-3 float-right">
                                <button type="submit" class="btn btn-primary " style="float:right">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.min.js"
        integrity="sha384-heAjqF+bCxXpCWLa6Zhcp4fu20XoNIA98ecBC1YkdXhszjoejr5y9Q77hIrv8R9i" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous">
    </script>
</body>

</html>
