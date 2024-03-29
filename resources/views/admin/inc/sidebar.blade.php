<div class="sidebar px-4 py-4 py-md-4 me-0">
    <div class="d-flex flex-column h-100">
        <a href="{{route('admin.dashboard')}}" class="mb-0 brand-icon">
            <span class="logo-icon">
                <i class="bi bi-bag-check-fill fs-4"></i>
            </span>
            <span class="logo-text">Fresh Cart</span>
        </a>
        <!-- Menu: main ul -->
        <ul class="menu-list flex-grow-1 mt-3">
            <li><a class="m-link
            @if(request()->is('admin/dashboard') || request()->is('admin/dashboard/*')) active @endif
            " href="{{route('admin.dashboard')}}"><i class="icofont-home fs-5"></i> <span>Dashboard</span></a></li>
            <li><a class="m-link @if(request()->is('admin/users') || request()->is('admin/users/*')) active @endif" href="{{route('admin.users.index')}}"><i class="icofont-home fs-5"></i> <span>Users</span></a></li>
            <li class="collapsed">
                <a class="m-link @if(request()->is('admin/delivery-boys') || request()->is('admin/delivery-boys/*')) active @endif" data-bs-toggle="collapse" data-bs-target="#menu-product" href="#">
                    <i class="icofont-truck-loaded fs-5"></i> <span>Delivery Boy</span> <span class="arrow icofont-rounded-down ms-auto text-end fs-5"></span></a>
                <!-- Menu: Sub menu ul -->
                <ul class="sub-menu collapse" id="menu-product">
                    <li><a class="ms-link" href="{{route('admin.delivery-boys.index')}}">List</a></li>
                    <li><a class="ms-link" href="{{route('admin.delivery-boys.create')}}">Add</a></li>
                </ul>
            </li>
            <li class="collapsed">
                <a class="m-link @if(request()->is('admin/categories') || request()->is('admin/categories/*')) active @endif" data-bs-toggle="collapse" data-bs-target="#categories" href="#">
                    <i class="icofont-chart-flow fs-5"></i> <span>Categories</span> <span class="arrow icofont-rounded-down ms-auto text-end fs-5"></span></a>
                <!-- Menu: Sub menu ul -->
                <ul class="sub-menu collapse" id="categories">
                    <li><a class="ms-link" href="{{route('admin.categories.index')}}">List</a></li>
                    <li><a class="ms-link" href="{{route('admin.categories.create')}}">Add</a></li>
                </ul>
            </li>
            <li class="collapsed">
                <a class="m-link" data-bs-toggle="collapse" data-bs-target="#menu-order" href="#">
                    <i class="icofont-notepad fs-5"></i> <span>Product</span> <span class="arrow icofont-rounded-down ms-auto text-end fs-5"></span></a>
                <!-- Menu: Sub menu ul -->
                <ul class="sub-menu collapse" id="menu-order">
                    <li><a class="ms-link" href="{{route('admin.products.index')}}"> List</a></li>
                    <li><a class="ms-link" href="{{route('admin.products.create')}}">Add</a></li>
                </ul>
            </li>
            <li class="collapsed">
                <a class="m-link" data-bs-toggle="collapse" data-bs-target="#customers-info" href="#">
                    <i class="icofont-funky-man fs-5"></i> <span>Orders</span> <span class="arrow icofont-rounded-down ms-auto text-end fs-5"></span></a>

                <ul class="sub-menu collapse" id="customers-info">
                    <li><a class="ms-link" href="{{route('admin.orders.new')}}">New</a></li>
                    <li><a class="ms-link" href="{{route('admin.orders.old')}}">Old</a></li>
                </ul>
            </li>
            <li class="collapsed">
                <a class="m-link" data-bs-toggle="collapse" data-bs-target="#menu-sale" href="#">
                    <i class="icofont-sale-discount fs-5"></i> <span>Report</span> <span class="arrow icofont-rounded-down ms-auto text-end fs-5"></span></a>
                <ul class="sub-menu collapse" id="menu-sale">
                    <li><a class="ms-link" href="{{route('admin.daily.purchase.reports.index')}}">Daily Purchase Report</a></li>
                    <li><a class="ms-link" href="{{route('admin.daily.order.reports.index')}}">Mandi Sheet</a></li>
                    <!-- <li><a class="ms-link" href="#">Report 3</a></li> -->
                </ul>
            </li>
            <li class="collapsed">
                <a class="m-link" data-bs-toggle="collapse" data-bs-target="#menu-inventory" href="#">
                    <i class="icofont-chart-histogram fs-5"></i> <span>Sliders</span> <span class="arrow icofont-rounded-down ms-auto text-end fs-5"></span></a>
                <ul class="sub-menu collapse" id="menu-inventory">
                    <li><a class="ms-link" href="{{route('admin.sliders.index')}}">List</a></li>
                    <li><a class="ms-link" href="{{route('admin.sliders.create')}}">Add</a></li>
                    <!-- <li><a class="ms-link" href="supplier.html">Supplier</a></li>
                    <li><a class="ms-link" href="returns.html">Returns</a></li>
                    <li><a class="ms-link" href="department.html">Department</a></li> -->
                </ul>
            </li>
            <li><a class="m-link @if(request()->is('admin/offers') || request()->is('admin/offers/*')) active @endif" href="{{route('admin.offers.index')}}"><i class="icofont-home fs-5"></i> <span>Offers</span></a></li>

            <li class="collapsed">
                <a class="m-link" data-bs-toggle="collapse" data-bs-target="#menu-Componentsone" href="#"><i class="icofont-ui-calculator"></i> <span>Setting</span> <span class="arrow icofont-rounded-down ms-auto text-end fs-5"></span></a>
                
                <ul class="sub-menu collapse" id="menu-Componentsone">
                <li><a class="ms-link" href="{{route('admin.settings.index')}}">List</a></li>                                        
                </ul>
            </li>
            <!-- <li class="collapsed">
                <a class="m-link" data-bs-toggle="collapse" data-bs-target="#app" href="#">
                    <i class="icofont-code-alt fs-5"></i> <span>App</span> <span class="arrow icofont-rounded-down ms-auto text-end fs-5"></span></a>
                
                <ul class="sub-menu collapse" id="app">
                    <li><a class="ms-link" href="calendar.html">Calandar</a></li>
                    <li><a class="ms-link" href="chat.html"> Chat App</a></li>
                </ul>
            </li> -->
            <!-- <li><a class="m-link" href="store-locator.html"><i class="icofont-focus fs-5"></i> <span>Store Locator</span></a></li>
            <li><a class="m-link" href="ui-elements/ui-alerts.html"><i class="icofont-paint fs-5"></i> <span>UI Components</span></a></li> -->
            <!-- <li class="collapsed">
                <a class="m-link" data-bs-toggle="collapse" data-bs-target="#page" href="#">
                    <i class="icofont-page fs-5"></i> <span>Other Pages</span> <span class="arrow icofont-rounded-down ms-auto text-end fs-5"></span></a>
                
                <ul class="sub-menu collapse" id="page">
                    <li><a class="ms-link" href="admin-profile.html">Profile Page</a></li>
                    <li><a class="ms-link" href="purchase-plan.html">Price Plan Example</a></li>
                    <li><a class="ms-link" href="charts.html">Charts Example</a></li>
                    <li><a class="ms-link" href="table.html">Table Example</a></li>
                    <li><a class="ms-link" href="forms.html">Forms Example</a></li>
                    <li><a class="ms-link" href="icon.html">Icons</a></li>
                    <li><a class="ms-link" href="contact.html">Contact Us</a></li>
                    <li><a class="ms-link" href="todo-list.html">Todo List</a></li>
                </ul>
            </li> -->
        </ul>
        <!-- Menu: menu collepce btn -->
        <button type="button" class="btn btn-link sidebar-mini-btn text-light">
            <span class="ms-2"><i class="icofont-bubble-right"></i></span>
        </button>
    </div>
</div>