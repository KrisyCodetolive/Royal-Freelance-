{{-- Display Conditions Panel --}}
@php
    $conditions = $block->display_conditions ?? ['operator' => 'AND', 'rules' => []];
    $operator = $conditions['operator'] ?? 'AND';
    $rules = $conditions['rules'] ?? [];
    
    $conditionTypes = [
        'date_range' => ['label' => 'Plage de dates', 'icon' => 'heroicon-o-calendar'],
        'time_range' => ['label' => 'Plage horaire', 'icon' => 'heroicon-o-clock'],
        'day_of_week' => ['label' => 'Jours de la semaine', 'icon' => 'heroicon-o-calendar-days'],
        'url_contains' => ['label' => 'URL contient', 'icon' => 'heroicon-o-link'],
        'url_equals' => ['label' => 'URL égale', 'icon' => 'heroicon-o-link'],
        'device' => ['label' => 'Type d\'appareil', 'icon' => 'heroicon-o-device-phone-mobile'],
        'referrer_contains' => ['label' => 'Référent contient', 'icon' => 'heroicon-o-arrow-top-right-on-square'],
        'cookie_exists' => ['label' => 'Cookie existe', 'icon' => 'heroicon-o-finger-print'],
        'cookie_equals' => ['label' => 'Cookie égal', 'icon' => 'heroicon-o-finger-print'],
        'visitor_count' => ['label' => 'Nombre de visites', 'icon' => 'heroicon-o-eye'],
        'is_logged_in' => ['label' => 'Utilisateur connecté', 'icon' => 'heroicon-o-user'],
        'context_variable' => ['label' => 'Variable contextuelle', 'icon' => 'heroicon-o-variable'],
    ];
@endphp

<div x-data="displayConditionsManager(@js($rules), @js($operator))" class="space-y-4">
    
    {{-- Header with status --}}
    <div class="flex items-center justify-between">
        <h4 class="font-medium text-gray-200 flex items-center gap-2">
            <x-heroicon-o-funnel class="w-4 h-4 text-amber-400" />
            Conditions d'affichage
        </h4>
        @if(count($rules) > 0)
            <span class="text-xs bg-amber-500/20 text-amber-400 px-2 py-1 rounded-full">
                {{ count($rules) }} règle(s)
            </span>
        @endif
    </div>

    @if(count($rules) > 0)
        {{-- Operator Toggle --}}
        <div class="flex items-center gap-2 p-3 bg-gray-800 rounded-lg">
            <span class="text-sm text-gray-400">Combiner les règles avec :</span>
            <div class="flex gap-2">
                <button wire:click="$parent.setDisplayConditionOperator({{ $block->id }}, 'AND')"
                        class="px-3 py-1 text-xs rounded-full transition {{ $operator === 'AND' ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-400 hover:bg-gray-600' }}">
                    ET (toutes)
                </button>
                <button wire:click="$parent.setDisplayConditionOperator({{ $block->id }}, 'OR')"
                        class="px-3 py-1 text-xs rounded-full transition {{ $operator === 'OR' ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-400 hover:bg-gray-600' }}">
                    OU (une)
                </button>
            </div>
        </div>

        {{-- Rules List --}}
        <div class="space-y-2">
            @foreach($rules as $index => $rule)
                @php
                    $ruleType = $rule['type'] ?? 'unknown';
                    $ruleConfig = $conditionTypes[$ruleType] ?? ['label' => ucfirst($ruleType), 'icon' => 'heroicon-o-question-mark-circle'];
                @endphp
                <div class="flex items-center gap-2 p-3 bg-gray-700 rounded-lg group">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 text-sm">
                            <span class="text-amber-400 font-medium">{{ $ruleConfig['label'] }}</span>
                        </div>
                        <div class="text-xs text-gray-400 mt-1">
                            @switch($ruleType)
                                @case('date_range')
                                    Du {{ $rule['start'] ?? '?' }} au {{ $rule['end'] ?? '?' }}
                                    @break
                                @case('time_range')
                                    De {{ $rule['start'] ?? '?' }} à {{ $rule['end'] ?? '?' }}
                                    @break
                                @case('day_of_week')
                                    @php
                                        $dayNames = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
                                        $days = array_map(fn($d) => $dayNames[$d] ?? '?', $rule['days'] ?? []);
                                    @endphp
                                    {{ implode(', ', $days) }}
                                    @break
                                @case('url_contains')
                                @case('url_equals')
                                @case('referrer_contains')
                                    "{{ $rule['value'] ?? '?' }}"
                                    @break
                                @case('device')
                                    {{ ucfirst($rule['value'] ?? 'desktop') }}
                                    @break
                                @case('cookie_exists')
                                    Cookie: {{ $rule['name'] ?? '?' }}
                                    @break
                                @case('cookie_equals')
                                    {{ $rule['name'] ?? '?' }} = "{{ $rule['value'] ?? '?' }}"
                                    @break
                                @case('visitor_count')
                                    {{ $rule['operator'] ?? '>=' }} {{ $rule['value'] ?? '1' }} visites
                                    @break
                                @case('is_logged_in')
                                    {{ ($rule['value'] ?? true) ? 'Oui' : 'Non' }}
                                    @break
                                @case('context_variable')
                                    {{ $rule['key'] ?? '?' }} {{ $rule['operator'] ?? '=' }} {{ $rule['value'] ?? '?' }}
                                    @break
                            @endswitch
                        </div>
                    </div>
                    <button wire:click="$parent.removeDisplayConditionRule({{ $block->id }}, {{ $index }})"
                            class="p-1 text-red-400 hover:text-red-300 opacity-0 group-hover:opacity-100 transition">
                        <x-heroicon-o-trash class="w-4 h-4" />
                    </button>
                </div>
            @endforeach
        </div>

        {{-- Clear All Button --}}
        <button wire:click="$parent.clearDisplayConditions({{ $block->id }})"
                class="w-full px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg transition text-sm flex items-center justify-center gap-2">
            <x-heroicon-o-x-circle class="w-4 h-4" />
            Effacer toutes les conditions
        </button>
    @endif

    {{-- Add New Rule --}}
    <div x-data="{ showAddRule: false, newRuleType: '', ruleData: {} }" class="space-y-3">
        <button @click="showAddRule = !showAddRule"
                class="w-full px-4 py-3 bg-gray-700 hover:bg-gray-600 rounded-lg transition text-sm flex items-center justify-center gap-2">
            <x-heroicon-o-plus class="w-4 h-4" />
            Ajouter une condition
        </button>
        
        <div x-show="showAddRule" x-collapse class="space-y-3 p-4 bg-gray-800 rounded-lg">
            {{-- Rule Type Selector --}}
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Type de condition</label>
                <select x-model="newRuleType"
                        @change="ruleData = {}"
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm">
                    <option value="">Sélectionner...</option>
                    @foreach($conditionTypes as $type => $config)
                        <option value="{{ $type }}">{{ $config['label'] }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Dynamic Fields based on Rule Type --}}
            <template x-if="newRuleType === 'date_range'">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Date début</label>
                        <input type="date" x-model="ruleData.start" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Date fin</label>
                        <input type="date" x-model="ruleData.end" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm">
                    </div>
                </div>
            </template>

            <template x-if="newRuleType === 'time_range'">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Heure début</label>
                        <input type="time" x-model="ruleData.start" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Heure fin</label>
                        <input type="time" x-model="ruleData.end" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm">
                    </div>
                </div>
            </template>

            <template x-if="newRuleType === 'day_of_week'">
                <div>
                    <label class="block text-xs text-gray-400 mb-2">Jours actifs</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] as $i => $day)
                            <label class="flex items-center gap-1 cursor-pointer">
                                <input type="checkbox" 
                                       x-model="ruleData.days"
                                       value="{{ $i }}"
                                       class="rounded bg-gray-700 border-gray-600 text-blue-500">
                                <span class="text-xs">{{ $day }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </template>

            <template x-if="['url_contains', 'url_equals', 'referrer_contains'].includes(newRuleType)">
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Valeur</label>
                    <input type="text" 
                           x-model="ruleData.value" 
                           placeholder="Ex: utm_source=facebook"
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm">
                </div>
            </template>

            <template x-if="newRuleType === 'device'">
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Type d'appareil</label>
                    <select x-model="ruleData.value" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm">
                        <option value="mobile">Mobile</option>
                        <option value="tablet">Tablette</option>
                        <option value="desktop">Desktop</option>
                    </select>
                </div>
            </template>

            <template x-if="newRuleType === 'cookie_exists'">
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Nom du cookie</label>
                    <input type="text" 
                           x-model="ruleData.name" 
                           placeholder="Ex: returning_visitor"
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm">
                </div>
            </template>

            <template x-if="newRuleType === 'cookie_equals'">
                <div class="space-y-2">
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Nom du cookie</label>
                        <input type="text" x-model="ruleData.name" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Valeur attendue</label>
                        <input type="text" x-model="ruleData.value" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm">
                    </div>
                </div>
            </template>

            <template x-if="newRuleType === 'visitor_count'">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Opérateur</label>
                        <select x-model="ruleData.operator" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm">
                            <option value=">=">≥ (au moins)</option>
                            <option value="=">= (exactement)</option>
                            <option value="<=">≤ (au plus)</option>
                            <option value=">">></option>
                            <option value="<"><</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Nombre</label>
                        <input type="number" x-model="ruleData.value" min="1" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm">
                    </div>
                </div>
            </template>

            <template x-if="newRuleType === 'is_logged_in'">
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Utilisateur doit être</label>
                    <select x-model="ruleData.value" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm">
                        <option value="true">Connecté</option>
                        <option value="false">Non connecté</option>
                    </select>
                </div>
            </template>

            <template x-if="newRuleType === 'context_variable'">
                <div class="space-y-2">
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Clé de la variable</label>
                        <input type="text" x-model="ruleData.key" placeholder="Ex: quiz_score" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Opérateur</label>
                            <select x-model="ruleData.operator" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm">
                                <option value="=">=</option>
                                <option value="!=">≠</option>
                                <option value=">">></option>
                                <option value=">=">≥</option>
                                <option value="<"><</option>
                                <option value="<=">≤</option>
                                <option value="contains">contient</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Valeur</label>
                            <input type="text" x-model="ruleData.value" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm">
                        </div>
                    </div>
                </div>
            </template>

            {{-- Add Button --}}
            <button @click="
                if(newRuleType) {
                    const rule = { type: newRuleType, ...ruleData };
                    if(newRuleType === 'day_of_week' && rule.days) {
                        rule.days = rule.days.map(d => parseInt(d));
                    }
                    if(newRuleType === 'is_logged_in') {
                        rule.value = rule.value === 'true';
                    }
                    $wire.$parent.addDisplayConditionRule({{ $block->id }}, rule);
                    showAddRule = false;
                    newRuleType = '';
                    ruleData = {};
                }
            "
                    x-bind:disabled="!newRuleType"
                    x-bind:class="{ 'opacity-50 cursor-not-allowed': !newRuleType }"
                    class="w-full px-4 py-2 bg-amber-600 hover:bg-amber-700 rounded-lg transition text-sm flex items-center justify-center gap-2">
                <x-heroicon-o-plus class="w-4 h-4" />
                Ajouter la règle
            </button>
        </div>
    </div>

    {{-- Info --}}
    <div class="text-xs text-gray-500 p-3 bg-gray-800/50 rounded-lg">
        <p>💡 Les conditions permettent d'afficher ce bloc uniquement si certains critères sont remplis (date, heure, appareil, URL, etc.)</p>
    </div>
</div>

<script>
    function displayConditionsManager(rules, operator) {
        return {
            rules: rules,
            operator: operator,
        }
    }
</script>
