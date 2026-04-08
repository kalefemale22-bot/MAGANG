<?php

namespace App\Helpers;

class JadwalHelper
{
    /**
     * Group consecutive jadwal entries with same guru_id + mapel_id.
     * Works for both "hari ini" (single day) and full week jadwal.
     *
     * @param array $jadwal Sorted jadwal entries
     * @param bool  $multiDay Whether the jadwal spans multiple days (uses 'hari' field for grouping)
     * @return array Grouped jadwal entries with jam_awal, jam_akhir, jadwal_ids
     */
    public static function group(array $jadwal, bool $multiDay = false): array
    {
        $grouped = [];

        foreach ($jadwal as $j) {
            $last = !empty($grouped) ? $grouped[count($grouped) - 1] : null;

            $sameGroup = false;
            if ($last) {
                $sameGroup = ($last['guru_id'] == $j['guru_id'])
                    && ($last['mapel_id'] == $j['mapel_id'])
                    && (($last['kelas_id'] ?? null) === ($j['kelas_id'] ?? null))
                    && (($last['rombel_id'] ?? null) === ($j['rombel_id'] ?? null));
            }

            // For multi-day jadwal, also check same day
            if ($multiDay && $sameGroup) {
                $sameGroup = ($last['hari'] === $j['hari']);
            }

            if ($sameGroup) {
                // Extend existing group
                $idx = count($grouped) - 1;
                $grouped[$idx]['jam_akhir'] = $j['jam_ke'];
                $grouped[$idx]['jam_selesai'] = $j['jam_selesai'];
                $grouped[$idx]['jadwal_ids'][] = $j['id'];
            } else {
                // New group
                $j['jam_awal'] = $j['jam_ke'];
                $j['jam_akhir'] = $j['jam_ke'];
                $j['jadwal_ids'] = [$j['id']];
                $grouped[] = $j;
            }
        }

        return $grouped;
    }

    /**
     * Get display label for jam range.
     * e.g., "1" for single jam, "1-3" for range.
     */
    public static function jamLabel(array $j): string
    {
        if ($j['jam_awal'] == $j['jam_akhir']) {
            return (string) $j['jam_awal'];
        }
        return $j['jam_awal'] . '-' . $j['jam_akhir'];
    }
}
