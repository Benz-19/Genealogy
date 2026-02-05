<?php
namespace App\Services\Genealogy;

interface GenealogyServiceInterface{
    public function linkMember(int $uplineId, int $downlineId): bool;
    public function getFullUpline(int $userId): array;
    public function getFullDownline(int $userId): array;
    public function getNetworkStats(int $userId): array;
    public function getFormattedTree(int $userId): array;
}