<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Melindastore &rsaquo; {{ Request::segment(2) === "print" ? "Invoice" : ucfirst(str_replace("-"," ",Request::segment(1))) }}</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
    integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
    integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="{{ asset('assets/modules/datatables/datatables.min.css') }}">
  <link rel="stylesheet"
    href="{{ asset('assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css') }}">

  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <link rel="stylesheet" href="//cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css">
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
  <link rel="stylesheet" href="{{asset('assets/modules/select2/dist/css/select2.min.css')}}">
  <link rel="stylesheet" href="{{asset('assets/modules/bootstrap-daterangepicker/daterangepicker.css')}}">
  <!-- Template CSS -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
  <style type="text/css">
  .requirement-validasi {
        border-color: #dc3545;
        padding-right: calc(1.5em + .75rem);
        background-image: url(data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23dc3545' viewBox='-2 -2 7 7'%3e%3cpath stroke='%23dc3545' d='M0 0l3 3m0-3L0 3'/%3e%3ccircle r='.5'/%3e%3ccircle cx='3' r='.5'/%3e%3ccircle cy='3' r='.5'/%3e%3ccircle cx='3' cy='3' r='.5'/%3e%3c/svg%3E);
        background-repeat: no-repeat;
        background-position: center right calc(.375em + .1875rem);
        background-size: calc(.75em + .375rem) calc(.75em + .375rem);
    }
  </style>
  @yield('css')
</head>

<body>
  <div id="app">
    <div class="main-wrapper">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar">
        <form class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
            <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i
                  class="fas fa-search"></i></a></li>
          </ul>
        </form>
        <ul class="navbar-nav navbar-right">
          <li class="dropdown"><a href="#" data-toggle="dropdown"
              class="nav-link dropdown-toggle nav-link-lg nav-link-user">
              <img alt="image" src="{{ asset('assets/img/avatar/avatar-1.png') }}" class="rounded-circle mr-1">
              <div class="d-sm-none d-lg-inline-block">Hi, {{ Auth::user()->name }}</div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
              <div class="dropdown-divider"></div>
              <a href="{{ route('logout') }}" onclick="event.preventDefault();
              document.getElementById('logout-form').submit();" class="dropdown-item has-icon text-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
              </a>

              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
              </form>
            </div>
          </li>
        </ul>
      </nav>
      <div class="main-sidebar">
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand">
            <a href="index.html">Melindastore</a>
          </div>
          <div class="sidebar-brand sidebar-brand-sm">
            <a href="index.html">Ms</a>
          </div>
          <ul class="sidebar-menu">
            @php
              $menu = array();
              if(auth()->user()->user_menu != ''){
                $menu = explode(',', auth()->user()->user_menu);
              }
            @endphp

            <li class="menu-header">Master</li>
            @if(count($menu) == 0 || in_array("Barang", $menu))
            <li class="{{ Request::segment(1) === "barang" ? "active" : "" }}"><a href="{{ url('barang') }}" class="nav-link"><i class="fas fa-cube"></i> <span>Barang</span></a></li>
            @endif
            @if(count($menu) == 0 || in_array("Kategori Barang", $menu))
            <li class="{{ Request::segment(1) === "kategori" ? "active" : "" }}"><a class="nav-link" href="{{ route('kategori') }}"><i class="far fa-square"></i> <span>Kategori
                  Barang</span></a>
            </li>
            @endif
            @if(count($menu) == 0 || in_array("Toko", $menu))
            <li class="{{ Request::segment(1) === "toko" ? "active" : "" }}"><a class="nav-link" href="{{ url('toko') }}"><i class="fas fa-th"></i> <span>Toko</span></a></li>
            @endif
             @if(count($menu) == 0 || in_array("Supplier", $menu))
            <li class="{{ Request::segment(1) === "toko" ? "active" : "" }}"><a class="nav-link" href="{{ url('supplier') }}"><i class="fas fa-briefcase"></i> <span>Supplier</span></a></li>
            @endif
             @if(count($menu) == 0 || in_array("Setting", $menu))
            <li class="{{ Request::segment(1) === "setting" ? "active" : "" }}"><a class="nav-link" href="{{ url('setting') }}"><i class="fas fa-check"></i> <span>Accounting</span></a></li>
            @endif
            <li class="menu-header">Barang</li>
            @if(count($menu) == 0 || in_array("Barang Masuk", $menu))
            <li class="{{ Request::segment(1) === "stock-in" ? "active" : "" }}"><a class="nav-link" href="{{ url('stock-in') }}"><i class="fas fa-archive"></i> <span>Barang Masuk</span></a></li>
            @endif
            @if(count($menu) == 0 || in_array("Barang Keluar", $menu))
            <li class="{{ Request::segment(1) === "barang-keluar" ? "active" : "" }}"><a class="nav-link" href="{{ url('barang-keluar') }}"><i class="fas fa-upload"></i> <span>Barang Keluar</span></a></li>
            @endif
            @if(count($menu) == 0 || in_array("Stock", $menu))
            <li class="{{ Request::segment(1) === "stock" ? "active" : "" }}"><a class="nav-link" href="{{ url('stock') }}"><i class="fas fa-database"></i> <span>Stock</span></a></li>
            @endif
            @if(count($menu) == 0 || in_array("Pindah Toko", $menu))
            <li class="nav-item dropdown {{ Request::segment(1) === "pindah-toko" ? "active" : "" }}">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-cubes"></i> <span>Pindah Toko</span></a>
              <ul class="dropdown-menu">
                <li class="{{ Request::segment(2) === "in" ? "active" : "" }}"><a class="nav-link" href="{{ url('pindah-toko/in') }}">Barang Masuk</a></li>
                <li class="{{ Request::segment(2) === "out" ? "active" : "" }}"><a class="nav-link" href="{{ url('pindah-toko/out') }}">Barang Keluar</a></li>
              </ul>
            </li>
            @endif
            @if(count($menu) == 0 || in_array("Search", $menu))
            <li class="{{ Request::segment(1) === "stock" ? "active" : "" }}"><a class="nav-link" href="{{ url('search') }}"><i class="fas fa-search"></i> <span>Search</span></a></li>
            @endif
            <li class="menu-header">Transaction</li>
            @if(count($menu) == 0 || in_array("Purchase Order", $menu))
            <li class="{{ Request::segment(1) === "po" ? "active" : "" }}"><a class="nav-link" href="{{url("po")}}"><i class="fas fa-file"></i> <span>Purchase Order</span></a></li>
            @endif
            @if(count($menu) == 0 || in_array("Penjualan", $menu))
            <li class="{{ Request::segment(1) === "penjualan" ? "active" : "" }}"><a class="nav-link" href="{{url("penjualan")}}"><i class="fas fa-calculator"></i> <span>Penjualan</span></a></li>
            @endif
            @if(count($menu) == 0 || in_array("Invoice", $menu))
            <li class="{{ Request::segment(1) === "invoice" ? "active" : "" }}"><a class="nav-link" href="{{url("invoice")}}"><i class="fas fa-file-invoice"></i> <span>Invoice</span></a></li>
            @endif
            @if(count($menu) == 0 || in_array("Quotation", $menu))
            <li class="{{ Request::segment(1) === "quotation" ? "active" : "" }}"><a class="nav-link" href="{{url("quotation")}}"><i class="fas fa-file"></i> <span>Quotation</span></a></li>
            @endif
            <li class="menu-header">Report</li>
            @if(count($menu) == 0 || in_array("Report", $menu))
              @if(Auth::user()->status_admin != 1)
                <li class="{{ Request::segment(1) === "report/barang-masuk" ? "active" : "" }}"><a href="{{ url('report/barang-masuk') }}" class="nav-link"><i class="fas fa-chart-bar"></i> <span>Report</span></a></li>
              @endif
            @endif
            <li class="menu-header">User Management</li>
            @if(count($menu) == 0 || in_array("User", $menu))
             @if(Auth::user()->status_admin != 1)
              <li class="{{ Request::segment(1) === "users" ? "active" : "" }}"><a href="{{ url('users') }}" class="nav-link"><i class="fas fa-users"></i> <span>Users</span></a></li>
              @endif
            @endif
            <li class="{{ Request::segment(1) === "signature" ? "active" : "" }}"><a href="{{ url('signature') }}" class="nav-link"><i class="fas fa-pen-nib"></i> <span>Tanda Tangan</span></a></li>
          </ul>
        </aside>
      </div>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          @yield('content')
        </section>
        @yield('modal')
      </div>
      <footer class="main-footer">
        <div class="footer-left">
          Copyright &copy; {{ date('Y') }} <div class="bullet"></div> Melindastore
        </div>
        <div class="footer-right">
          {{-- 2.3.0 --}}
        </div>
      </footer>
    </div>
  </div>
  @yield('javascript')
  <script type="text/javascript">
  $(document).on('keyup', '.money-format', function(event) {
    // skip for arrow keys
    if(event.which >= 37 && event.which <= 40) return;
    // format number
    $(this).val(function(index, value) {
      return value
      .replace(/\D/g, "")
      .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
      ;
    });
  });
 </script>
  <!-- General JS Scripts -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
    integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
  </script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>
  <script src="{{ asset('assets/js/stisla.js') }}"></script>
  <script src="{{ asset('assets/modules/datatables/datatables.min.js') }}"></script>
  <script src="{{ asset('assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js') }}"></script>
  <script src="{{ asset('assets/modules/datatables/jquery-ui/jquery-ui.min.js') }}"></script>
  <script src="{{asset('assets/modules/select2/dist/js/select2.full.js')}}"></script>
  <script src="{{asset('assets/modules/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
  <!-- Template JS File -->
  {{-- <script src="{{ asset('assets/js/scripts.js') }}"></script> --}}
  {{-- <script src="{{ asset('assets/js/custom.js') }}"></script> --}}
  <script type="text/javascript">
    "use strict";

    $(function() {
      let sidebar_nicescroll_opts = {
        cursoropacitymin: 0,
        cursoropacitymax: .8,
        zindex: 892
      }, now_layout_class = null;

      var sidebar_sticky = function() {
        if($("body").hasClass('layout-2')) {
          $("body.layout-2 #sidebar-wrapper").stick_in_parent({
            parent: $('body')
          });
          $("body.layout-2 #sidebar-wrapper").stick_in_parent({recalc_every: 1});
        }
      }
      sidebar_sticky();

      var sidebar_nicescroll;
      var update_sidebar_nicescroll = function() {
        let a = setInterval(function() {
          if(sidebar_nicescroll != null)
            sidebar_nicescroll.resize();
        }, 10);

        setTimeout(function() {
          clearInterval(a);
        }, 600);
      }

      var sidebar_dropdown = function() {
        if($(".main-sidebar").length) {
          $(".main-sidebar").niceScroll(sidebar_nicescroll_opts);
          sidebar_nicescroll = $(".main-sidebar").getNiceScroll();

          $(".main-sidebar .sidebar-menu li a.has-dropdown").off('click').on('click', function() {
            var me     = $(this);
            var active = false;
            if(me.parent().hasClass("active")){
              active = true;
            }
            
            $('.main-sidebar .sidebar-menu li.active > .dropdown-menu').slideUp(500, function() {
              update_sidebar_nicescroll();          
              return false;
            });
            
            $('.main-sidebar .sidebar-menu li.active').removeClass('active');

            if(active==true) {
              me.parent().removeClass('active');          
              me.parent().find('> .dropdown-menu').slideUp(500, function() {            
                update_sidebar_nicescroll();
                return false;
              });
            }else{
              me.parent().addClass('active');          
              me.parent().find('> .dropdown-menu').slideDown(500, function() {            
                update_sidebar_nicescroll();
                return false;
              });
            }

            return false;
          });

          $('.main-sidebar .sidebar-menu li.active > .dropdown-menu').slideDown(500, function() {
            update_sidebar_nicescroll();        
            return false;
          });
        }
      }
      sidebar_dropdown();

      if($("#top-5-scroll").length) {
        $("#top-5-scroll").css({
          height: 315
        }).niceScroll();
      }

      $(".main-content").css({
        minHeight: $(window).outerHeight() - 108
      })

      $(".nav-collapse-toggle").click(function() {
        $(this).parent().find('.navbar-nav').toggleClass('show');
        return false;
      });

      $(document).on('click', function(e) {
        $(".nav-collapse .navbar-nav").removeClass('show');
      });

      var toggle_sidebar_mini = function(mini) {
        let body = $('body');

        if(!mini) {
          body.removeClass('sidebar-mini');
          $(".main-sidebar").css({
            overflow: 'hidden'
          });
          setTimeout(function() {
            $(".main-sidebar").niceScroll(sidebar_nicescroll_opts);
            sidebar_nicescroll = $(".main-sidebar").getNiceScroll();
          }, 500);
          $(".main-sidebar .sidebar-menu > li > ul .dropdown-title").remove();
          $(".main-sidebar .sidebar-menu > li > a").removeAttr('data-toggle');
          $(".main-sidebar .sidebar-menu > li > a").removeAttr('data-original-title');
          $(".main-sidebar .sidebar-menu > li > a").removeAttr('title');
        }else{
          body.addClass('sidebar-mini');
          body.removeClass('sidebar-show');
          sidebar_nicescroll.remove();
          sidebar_nicescroll = null;
          $(".main-sidebar .sidebar-menu > li").each(function() {
            let me = $(this);

            if(me.find('> .dropdown-menu').length) {
              me.find('> .dropdown-menu').hide();
              me.find('> .dropdown-menu').prepend('<li class="dropdown-title pt-3">'+ me.find('> a').text() +'</li>');
            }else{
              me.find('> a').attr('data-toggle', 'tooltip');
              me.find('> a').attr('data-original-title', me.find('> a').text());
              $("[data-toggle='tooltip']").tooltip({
                placement: 'right'
              });
            }
          });
        }
      }

      $("[data-toggle='sidebar']").click(function() {
        var body = $("body"),
          w = $(window);

        if(w.outerWidth() <= 1024) {
          body.removeClass('search-show search-gone');
          if(body.hasClass('sidebar-gone')) {
            body.removeClass('sidebar-gone');
            body.addClass('sidebar-show');
          }else{
            body.addClass('sidebar-gone');
            body.removeClass('sidebar-show');
          }

          update_sidebar_nicescroll();
        }else{
          body.removeClass('search-show search-gone');
          if(body.hasClass('sidebar-mini')) {
            toggle_sidebar_mini(false);
          }else{
            toggle_sidebar_mini(true);
          }
        }

        return false;
      });

      var toggleLayout = function() {
        var w = $(window),
          layout_class = $('body').attr('class') || '',
          layout_classes = (layout_class.trim().length > 0 ? layout_class.split(' ') : '');

        if(layout_classes.length > 0) {
          layout_classes.forEach(function(item) {
            if(item.indexOf('layout-') != -1) {
              now_layout_class = item;
            }
          });
        }

        if(w.outerWidth() <= 1024) {
          if($('body').hasClass('sidebar-mini')) {
            toggle_sidebar_mini(false);
            $('.main-sidebar').niceScroll(sidebar_nicescroll_opts);
            sidebar_nicescroll = $(".main-sidebar").getNiceScroll();
          }

          $("body").addClass("sidebar-gone");
          $("body").removeClass("layout-2 layout-3 sidebar-mini sidebar-show");
          $("body").off('click touchend').on('click touchend', function(e) {
            if($(e.target).hasClass('sidebar-show') || $(e.target).hasClass('search-show')) {
              $("body").removeClass("sidebar-show");
              $("body").addClass("sidebar-gone");
              $("body").removeClass("search-show");

              update_sidebar_nicescroll();
            }
          });

          update_sidebar_nicescroll();

          if(now_layout_class == 'layout-3') {
            let nav_second_classes = $(".navbar-secondary").attr('class'),
              nav_second = $(".navbar-secondary");

            nav_second.attr('data-nav-classes', nav_second_classes);
            nav_second.removeAttr('class');
            nav_second.addClass('main-sidebar');

            let main_sidebar = $(".main-sidebar");
            main_sidebar.find('.container').addClass('sidebar-wrapper').removeClass('container');
            main_sidebar.find('.navbar-nav').addClass('sidebar-menu').removeClass('navbar-nav');
            main_sidebar.find('.sidebar-menu .nav-item.dropdown.show a').click();
            main_sidebar.find('.sidebar-brand').remove();
            main_sidebar.find('.sidebar-menu').before($('<div>', {
              class: 'sidebar-brand'
            }).append(
              $('<a>', {
                href: $('.navbar-brand').attr('href'),
              }).html($('.navbar-brand').html())
            ));
            setTimeout(function() {
              sidebar_nicescroll = main_sidebar.niceScroll(sidebar_nicescroll_opts);
              sidebar_nicescroll = main_sidebar.getNiceScroll();
            }, 700);

            sidebar_dropdown();
            $(".main-wrapper").removeClass("container");
          }
        }else{
          $("body").removeClass("sidebar-gone sidebar-show");
          if(now_layout_class)
            $("body").addClass(now_layout_class);

          let nav_second_classes = $(".main-sidebar").attr('data-nav-classes'),
            nav_second = $(".main-sidebar");

          if(now_layout_class == 'layout-3' && nav_second.hasClass('main-sidebar')) {
            nav_second.find(".sidebar-menu li a.has-dropdown").off('click');
            nav_second.find('.sidebar-brand').remove();
            nav_second.removeAttr('class');
            nav_second.addClass(nav_second_classes);

            let main_sidebar = $(".navbar-secondary");
            main_sidebar.find('.sidebar-wrapper').addClass('container').removeClass('sidebar-wrapper');
            main_sidebar.find('.sidebar-menu').addClass('navbar-nav').removeClass('sidebar-menu');
            main_sidebar.find('.dropdown-menu').hide();
            main_sidebar.removeAttr('style');
            main_sidebar.removeAttr('tabindex');
            main_sidebar.removeAttr('data-nav-classes');
            $(".main-wrapper").addClass("container");
            // if(sidebar_nicescroll != null)
            //   sidebar_nicescroll.remove();
          }else if(now_layout_class == 'layout-2') {
            $("body").addClass("layout-2");
          }else{
            update_sidebar_nicescroll();
          }
        }
      }
      toggleLayout();
      $(window).resize(toggleLayout);

      $("[data-toggle='search']").click(function() {
        var body = $("body");

        if(body.hasClass('search-gone')) {
          body.addClass('search-gone');
          body.removeClass('search-show');
        }else{
          body.removeClass('search-gone');
          body.addClass('search-show');
        }
      });

      // tooltip
      $("[data-toggle='tooltip']").tooltip();

      // popover
      $('[data-toggle="popover"]').popover({
        container: 'body'
      });
    });

  </script>
</body>

</html>
