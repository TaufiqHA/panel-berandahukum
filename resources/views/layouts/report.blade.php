<div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <h4>Jump To</h4>
          </div>
          <div class="card-body">
            <ul class="nav nav-pills flex-column">
              <li class="nav-item"><a href="{{ url('report/barang-masuk') }}" class="nav-link {{ Request::segment(2) === "barang-masuk" ? "active" : "" }}">Laporan Barang Masuk</a></li>
              <li class="nav-item"><a href="{{ url('report/penjualan') }}" class="nav-link {{ Request::segment(2) === "penjualan" ? "active" : "" }}">Laporan Penjualan</a></li>
              <li class="nav-item"><a href="{{ url('report/pindah-barang') }}" class="nav-link {{ Request::segment(2) === "pindah-barang" ? "active" : "" }}">Laporan Perpindahan Barang</a></li>
              <li class="nav-item"><a href="{{ url('report/stock') }}" class="nav-link {{ Request::segment(2) === "stock" ? "active" : "" }}">Laporan Stock</a></li>
              <li class="nav-item"><a href="{{ url('report/laba-rugi') }}" class="nav-link {{ Request::segment(2) === "laba-rugi" ? "active" : "" }}">Laporan Laba Rugi</a></li>
              <li class="nav-item"><a href="{{ url('report/po') }}" class="nav-link {{ Request::segment(2) === "po" ? "active" : "" }}">Laporan Purchase Order</a></li>
            </ul>
          </div>
        </div>
      </div>
