<?php
$data = preg_split("/\r\n|\n|\r/", file_get_contents('20-data-sample.txt'));
$configurationData = array_map(fn($line) => explode(' -> ', $line), $data);

$configurations = [];
foreach ($configurationData as $configurationEntry) {
    $configurationName = $configurationEntry[0];
    if ($configurationName !== 'broadcaster') {
        $configurationName = substr($configurationName, 1);
    }
    $subModules = explode(', ', $configurationEntry[1]);

    $configurations[$configurationName] = [
        'on' => false,
        'type' => $configurationName !== 'broadcaster' ? substr($configurationEntry[0], 0, 1) : 'b',
        'pulse' => 'low',
        'modules' => $subModules
    ];
}

foreach ($configurations as $name => $configuration) {
    if ($configuration['type'] === '&') {
        $connectedInputs = array_filter($configurations, fn($con) => in_array($name, $con['modules']));
        foreach ($connectedInputs as $inputName => $connectedInput) {
            $configurations[$name]['modulesPulses'][$inputName] = 'low';
        }
    }
}

function pulseValueForConfiguration($module, $configurations, $pulses = [], $pulse = 'low', $caller = 'button'): array
{
    if (!isset($configurations[$module])) {
        return $pulses;
    }

    switch ($configurations[$module]['type']) {
        case 'b':
            $pulses[$pulse]++;
            foreach ($configurations[$module]['modules'] as $subModule) {
                $pulses[$configurations[$subModule]['pulse']]++;
                $configurations[$subModule]['pulse'] = $configurations[$subModule]['pulse'] === 'low' ? 'high' : 'low';
            }
            break;

        case '%':
            $pulses[$pulse]++;

            foreach ($configurations[$module]['modules'] as $subModule) {
                if (isset($configurations[$subModule])) {
                    $configurations[$subModule]['pulse'] = $pulse === 'low' ? 'low' : 'high';
                }
            }
            break;

        case '&':
            $pulses[$pulse]++;
            $firstSubModulesPulse = $configurations[$module]['modulesPulses'][array_key_first($configurations[$module]['modulesPulses'])];
            $allPulsesAreHigh = count(array_unique($configurations[$module]['modulesPulses'])) === 1 && $firstSubModulesPulse === 'high';

            foreach ($configurations[$module]['modules'] as $subModule) {
                if (isset($configurations[$subModule])) {
                    $configurations[$subModule]['pulse'] = $allPulsesAreHigh ? 'low' : 'high';
                }
            }

            break;

    }

    foreach ($configurations[$module]['modules'] as $subModule) {
        if (!isset($configurations[$subModule])) {
            continue;
        }
        if ($configurations[$subModule]['type'] === '%' && $configurations[$subModule]['pulse'] === 'high' && $configurations[$module]['type'] !== 'b') {
            continue;
        }
        if ($configurations[$subModule]['type'] === '&') {
            $configurations[$subModule]['modulesPulses'][$module] = $configurations[$module]['pulse'];
        }

        $pulses = pulseValueForConfiguration($subModule, $configurations, $pulses, $configurations[$subModule]['pulse'], $module);
    }

    return $pulses;
}

//TODO: My approach worked for the first example data, but with the second data I probably need and array/queue
//TODO: And keep executing the queue items...
$pulses = ['low' => 0, 'high' => 0];
for ($i = 0; $i < 4; $i++) {
    $pulses = pulseValueForConfiguration('broadcaster', $configurations, $pulses);
}
echo $pulses['low'] * $pulses['high'];
