<!-- Footer Section -->
<footer class="spotlight_footer_footer-section">
    <div class="container">
        <div class="row">

            <!-- Column 1: Brand & Logo -->
            <div class="col-lg-3 col-md-6 spotlight_footer_footer-column">
                <div class="text-start">
                    <!-- Using a generic icon to represent the logo in the image -->
                    <div class="spotlight_footer_brand-logo-icon">
                        <img src="{{asset('/')}}public/logo_white.png" alt="Logo">
                    </div>
                    <div class="spotlight_footer_brand-name">Spotlight Attires</div>
                    <div class="spotlight_footer_brand-phone">+8801965665880</div>

                    <div class="spotlight_footer_social-icons">
                        @if(isset($socialLinks) && !$socialLinks->isEmpty())
                        @foreach($socialLinks as $link)
                        <a href="{{ $link->link }}" target="_blank" title="{{ $link->title }}"><i
                                class="bi bi-{{ strtolower($link->title) }}"></i></a>
                        @endforeach
                        @endif
                    </div>

                </div>
            </div>

            <!-- Column 2: Quick Links -->
            <div class="col-lg-3 col-md-6 spotlight_footer_footer-column">
                <h4>QUICK LINKS</h4>
                <ul class="spotlight_footer_footer-links">
                    <li><a href="{{route('privacy_policy.show')}}">Privacy Policy</a></li>
                    <li><a href="{{route('term_and_condition.show')}}">Terms & Condition</a></li>
                    <li><a href="{{route('return_policy.show')}}">Return Policy</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>
            </div>

            <!-- Column 3: Contact Us -->
            <div class="col-lg-3 col-md-6 spotlight_footer_footer-column">
                <h4>CONTACT US</h4>
                <div class="spotlight_footer_contact-item">
                    <div class="spotlight_footer_contact-icon"><i class="bi bi-map"></i></div>
                    <span>{{$front_ins_add}}</span>
                </div>
                <div class="spotlight_footer_contact-item">
                    <div class="spotlight_footer_contact-icon"><i class="bi bi-phone"></i></div>
                    <span>
                        {{$front_ins_phone}}
                        @if(!empty($front_ins_phone_one))
                        <br>{{$front_ins_phone_one}}
                        @endif
                    </span>
                </div>
                <div class="spotlight_footer_contact-item">
                    <div class="spotlight_footer_contact-icon"><i class="bi bi-envelope"></i></div>
                    <span>
                        {{$front_ins_email}}
                        {{-- Display secondary email if it exists --}}
                        @if(!empty($front_ins_email_one))
                        <br>{{$front_ins_email_one}}
                        @endif
                    </span>
                </div>
            </div>

            <!-- Column 4: Facebook Page Plugin -->
            <div class="col-lg-3 col-md-6 spotlight_footer_footer-column">
                <iframe
                    src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Fspotlightattiresbangladesh&tabs=timeline&width=340&height=500&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId"
                    width="100%" height="300" style="border:none;overflow:hidden" scrolling="no" frameborder="0"
                    allowfullscreen="true"
                    allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
            </div>

        </div>
    </div>
</footer>

<!-- Copyright Bar -->
<div class="spotlight_footer_copyright-bar">
    <div class="container">
        <p class="spotlight_footer_copyright-text">&copy; 2025 Spotlight Attires.ALL RIGHTS RESERVED.</p>
    </div>
</div>