<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="{{asset('assets/css/welcome.css'); }}" type="text/css" media="all" />
        <!-- Styles -->
        <style>
            body{
                background: url({{asset('assets/images/610x800.png'); }}) right top no-repeat;
            }
            @media (min-width: 1440px) {
                body {
                    background: url({{asset('assets/images/logo-jr.png')}}) right top no-repeat;
                }
            }
        </style>
        
    </head>
    <body class="antialiased">
		<div class="container">
			<header>
				<img class="logo"  src="{{asset('assets/images/logo-name.png'); }}" alt="logo" />
			</header>
			<section class="hero">
				<img  src="{{asset('assets/images/logo-jr-mobile.png'); }}" alt="hero-image" />
			</section>
			<main>
				<div class="wrapper">
					<h1 class="heading">
						<span>We're</span> <br />coming<br />
						soon
					</h1>
					<p class="lead">
						Hello fellow shoppers! We're currently building our new fashion
						store. Add your email below to stay up-to-date with announcements
						and our launch deals.
					</p>
					<form action="" method="get" accept-charset="utf-8">
						<input
							type="text"
							name="email"
							id="email"
							value=""
							placeholder="Email Address"
						/>
						<div>
							<img
								id="error-icon"
                                src="{{asset('assets/images/icon-error.svg'); }}"
								alt="error-icon"
							/>
							<button class="btn" type="submit">
								<img  src="{{asset('assets/images/icon-arrow.svg'); }}" alt="submit-arrow" />
							</button>
						</div>
						<p id="error">Please provide a valid email</p>
					</form>
				</div>
			</main>
		</div>
    </body>
</html>
