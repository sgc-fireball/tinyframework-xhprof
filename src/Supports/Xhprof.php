<?php declare(strict_types=1);

namespace TinyFramework\Xhprof\Supports;

use ArrayAccess;
use RuntimeException;

class Xhprof implements ArrayAccess
{

    private ?string $id;

    private array $meta;

    private array $profile;

    public function __construct(array $xhprof = [])
    {
        $this->id = $xhprof['_id'] ?? null;
        $this->meta = $xhprof['meta'] ?? [];
        $this->profile = $xhprof['profile'] ?? [];
        $this->optimise();
    }

    private function optimise()
    {
        $totalCalls = 0;
        $main = $this->profile['main()'] = $this->profile['main()'] ?? ['ct' => 1, 'wt' => 1, 'cpu' => 1, 'mu' => 1];
        $main['ct'] = max(1, $main['ct']);
        $main['wt'] = max(1, $main['wt']);
        $main['cpu'] = max(1, $main['cpu']);
        $main['mu'] = max(1, $main['mu']);

        foreach ($this->profile as $call => &$data) {
            $totalCalls += $data['ct'];

            $callee = $call;
            if (str_contains($callee, '==>')) {
                [$caller, $callee] = explode('==>', $call, 2);
            }

            $data['children'] = [];
            $data['caller'] = isset($caller) ? $caller : 'main()';
            $data['callee'] = preg_replace('/^(.*)::(.*){closure}(@\d+)?$/', '\\1::{Closure}', $callee);
            $data['ewt'] = $data['wt'];
            $data['ecpu'] = $data['cpu'];
            $data['emu'] = $data['mu'];
            $data['wtp'] = round($data['wt'] / $main['wt'] * 100) . ' %';
            $data['cpup'] = round($data['cpu'] / $main['cpu'] * 100) . ' %';
            $data['mup'] = round($data['mu'] / $main['mu'] * 100) . ' %';

            foreach ($this->profile as $ccall => &$cdata) {
                if (str_starts_with($ccall, $callee . '==>')) {
                    $data['children'][$ccall] = &$cdata;
                    $data['ewt'] -= $cdata['wt'];
                    $data['ecpu'] -= $cdata['cpu'];
                    $data['emu'] -= $cdata['mu'];
                }
            }

            $data['ewt'] = max(-1, $data['ewt']);
            $data['ecpu'] = max(-1, $data['ecpu']);
            $data['emu'] = max(-1, $data['emu']);

            $data['ewtp'] = round($data['ewt'] / $main['wt'] * 100) . ' %';
            $data['ecpup'] = round($data['ecpu'] / $main['cpu'] * 100) . ' %';
            $data['emup'] = round($data['emu'] / $main['mu'] * 100) . ' %';
        }
        foreach ($this->profile as $call => &$data) {
            $data['ctp'] = round($data['ct'] / $totalCalls * 100) . ' %';
        }
    }

    public function thresholdCount(int $ct): Xhprof
    {
        $this->profile = array_filter($this->profile, function ($data) use ($ct) {
            return $data['ct'] >= $ct;
        });
        return $this;
    }

    public function thresholdWallTime(int $wt): Xhprof
    {
        $this->profile = array_filter($this->profile, function ($data) use ($wt) {
            return $data['wt'] >= $wt;
        });
        return $this;
    }

    public function thresholdCpu(int $cpu): Xhprof
    {
        $this->profile = array_filter($this->profile, function ($data) use ($cpu) {
            return $data['cpu'] >= $cpu;
        });
        return $this;
    }

    public function thresholdMemoryUse(int $mu): Xhprof
    {
        $this->profile = array_filter($this->profile, function ($data) use ($mu) {
            return $data['mu'] >= $mu;
        });
        return $this;
    }

    public function toArray(): array
    {
        return $this->profile;
    }

    public function offsetExists(mixed $offset): bool
    {
        return property_exists($this, $offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->offsetExists($offset) ? $this->{$offset} : null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new RuntimeException('Unsupported function.');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new RuntimeException('Unsupported function.');
    }

}
