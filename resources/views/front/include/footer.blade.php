    <footer class="footer-section">
        <div class="container">
            <div class="row">
                <!-- Contact Cards -->
                <div class="col-md-4">
                    <div class="contact-card">
                        <div class="icon-circle">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div class="contact-info">
                            {{$front_ins_add}}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-card">
                        <div class="icon-circle">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div class="contact-info">
                           {{$front_ins_email}}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-card">
                        <div class="icon-circle">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <div class="contact-info">
                         {{$front_ins_phone}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <!-- Footer Logo and Socials -->
                <div class="col-lg-4">
                    <div class="footer-logo-container">
                        <img src="{{$front_ins_url}}public/black.png" alt="Spotlight Attires"
                            class="footer-logo">
                        <div class="footer-social-icons">
                            @if(isset($socialLinks) && !$socialLinks->isEmpty())
                                @foreach($socialLinks as $link)
                                    <a href="{{ $link->link }}" target="_blank" title="{{ $link->title }}"><i class="bi bi-{{ strtolower($link->title) }}"></i></a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Pages Links -->
                <div class="col-lg-2 col-md-4">
                    <div class="footer-heading">Pages</div>
                    <ul class="list-unstyled footer-links">
                        <li><a href="{{route('home.index')}}">Home</a></li>
                        <li><a href="page_underconstruction.php">SDG & Innovations</a></li>
                        <li><a href="page_underconstruction.php">News & Article</a></li>
                        <li><a href="page_underconstruction.php">Career</a></li>
                    </ul>
                </div>

                <!-- Services Links -->
                <div class="col-lg-2 col-md-4">
                    <div class="footer-heading">Services</div>
                    <ul class="list-unstyled footer-links">
                        <li><a href="page_underconstruction.php">Renewable Energy</a></li>
                        <li><a href="page_underconstruction.php">xxxx</a></li>
                        <li><a href="page_underconstruction.php">xxxx</a></li>
                        <li><a href="page_underconstruction.php">xxxx</a></li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div class="col-lg-4 col-md-4">
                    <div class="footer-heading">Newsletter</div>
                    <p class="text-secondary" style="font-size: 0.9rem;">Subscribe our newsletter to get our latest
                        update & news</p>
                    <div class="newsletter-input-group">
                        <input type="email" class="form-control" placeholder="Email Address">
                        <button class="btn btn-send" type="button"><i class="bi bi-send-fill"></i></button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="footer-bottom-text">
                        Copyright© 2025 Spotlight Attires
                    </div>
                </div>
            </div>
        </div>
    </footer>