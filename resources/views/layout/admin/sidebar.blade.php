@php
    $userClass = $vendorClass = $invoiceClass = $productClass = $ocrClass = $categoryClass = $dashboardClass = $companyClass =  '';
    $urlPath = request()->segment(1);
    if($urlPath == 'dashboard'){
        $dashboardClass = 'active menu-open';
    }
    elseif($urlPath == 'user'){
        $userClass = 'active menu-open';
    }
    elseif($urlPath == 'vendor'){
        $vendorClass = 'active menu-open';
    }
    elseif($urlPath == 'invoice'){
        $invoiceClass = 'active menu-open';
    }
    elseif($urlPath == 'product'){
        $productClass = 'active menu-open';
    }
    elseif($urlPath == 'ocr'){
        $ocrClass = 'active menu-open';
    }
    elseif($urlPath == 'category'){
        $vendorClass = 'active menu-open';
        $categoryClass = 'active menu-open';
    }
    elseif($urlPath == 'company'){
        $companyClass = 'active menu-open';
    }
@endphp

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/dashboard" class="brand-link">
        <img src="{{asset('assets/img/AdminLTELogo.png') }}"
           alt="AdminLTE Logo"
           class="brand-image img-circle elevation-3"
           style="opacity: .8">
        <span class="brand-text font-weight-light">Invoice System</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-item has-treeview {{ $dashboardClass }}">

                    <a href="/dashboard" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Dashboard </p>
                    </a>

                </li>

                @if( auth()->user()->is_admin == 1)
                <li class="nav-item has-treeview {{ $userClass }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Users<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('userList') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Users List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('addUser') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add User</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                <li class="nav-item has-treeview {{ $companyClass }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon far fa-building"></i>
                        <p>Companies<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('companyList') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Companies List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('addCompany') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Company</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview {{ $vendorClass }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Vendors<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('vendorList') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Vendors List</p>
                            </a>
                        </li>
                        @if (auth()->user()->is_admin == 1)
                        <li class="nav-item">
                            <a href="{{ route('addVendor') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Vendor</p>
                            </a>
                        </li>
                        @endif
                        @if (auth()->user()->is_admin == 1)
                            <li class="nav-item has-treeview {{ $categoryClass }}">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-list"></i>
                                    <p>Categories<i class="right fas fa-angle-left"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('categoryList') }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Categories List</p>
                                        </a>
                                    </li>

                                        <li class="nav-item">
                                            <a href="{{ route('addCategory') }}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Add Category</p>
                                            </a>
                                        </li>

                                </ul>
                            </li>
                        @endif
                    </ul>
                </li>

                <li class="nav-item has-treeview {{ $invoiceClass }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-credit-card"></i>
                        <p>Invoices<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('invoiceList') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Invoices List</p>
                            </a>
                        </li>
                        @if (auth()->user()->is_admin == 1)
                        <li class="nav-item">
                            <a href="{{ route('addInvoice') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Invoice</p>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                <li class="nav-item has-treeview {{ $productClass }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-database"></i>
                        <p>Products<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('productList') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Products List</p>
                            </a>
                        </li>
                        @if (auth()->user()->is_admin == 1)
                        <li class="nav-item">
                            <a href="{{ route('addProduct') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Product</p>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
<!--               <li class="nav-item has-treeview  $ocrClass }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-database"></i>
                            <p>OCR<i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href=" route('ocr.index') }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>OCR's</p>
                                </a>
                            </li>
                            //if (auth()->user()->is_admin == 1)
                                <li class="nav-item">
                                    <a href=" route('ocr.create') }}" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Add OCR</p>
                                    </a>
                                </li>
                            endif
                        </ul>
                    </li>-->
            </ul>
        </nav>
    </div>
</aside>
