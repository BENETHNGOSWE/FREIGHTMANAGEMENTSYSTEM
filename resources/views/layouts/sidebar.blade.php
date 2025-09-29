<div class="deznav">
    <div class="deznav-scroll">
        <ul class="metismenu" id="menu">
            @foreach ($menu as $item)
                @php
                    // Check if the parent menu item has a permission key and if the user has that permission
                    $hasPermission = isset($item['permission']) && auth()->user()->hasPermissionTo($item['permission']);
                    
                    // The dashboard item has a null permission, so it should always be visible.
                    if ($item['permission'] === null) {
                        $hasPermission = true;
                    }
                    
                    // Also check if any child item has a permission the user can access
                    $hasVisibleChildren = false;
                    if (isset($item['children'])) {
                        foreach ($item['children'] as $child) {
                            if (isset($child['permission']) && auth()->user()->hasPermissionTo($child['permission'])) {
                                $hasVisibleChildren = true;
                                break;
                            }
                            // Handle child items with a null permission (if you add any)
                            if (!isset($child['permission']) || $child['permission'] === null) {
                                $hasVisibleChildren = true;
                                break;
                            }
                        }
                    }
                @endphp

                @if ($hasPermission || $hasVisibleChildren)
                    <li>
                        @if (isset($item['children']))
                            <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                                <i class="{{ $item['icon'] }}"></i>
                                <span class="nav-text">{{ $item['title'] }}</span>
                            </a>
                            <ul aria-expanded="false">
                                @foreach ($item['children'] as $child)
                                    @if (isset($child['permission']) && auth()->user()->hasPermissionTo($child['permission']))
                                        <li>
                                            <a href="{{ route($child['route']) }}">
                                                <span>{{ $child['title'] }}</span>
                                            </a>
                                        </li>
                                    @elseif (!isset($child['permission']) || $child['permission'] === null)
                                        {{-- This handles child items that have no permission requirement --}}
                                        <li>
                                            <a href="{{ route($child['route']) }}">
                                                <span>{{ $child['title'] }}</span>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @else
                            <a href="{{ route($item['route']) }}" class="ai-icon" aria-expanded="false">
                                <i class="{{ $item['icon'] }}"></i>
                                <span class="nav-text">{{ $item['title'] }}</span>
                            </a>
                        @endif
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</div>





