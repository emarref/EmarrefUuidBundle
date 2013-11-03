<?php

namespace Emarref\Bundle\UuidBundle\Uuid;

/**
 * MySQL UUID format utilities
 *
 * The three formats this class deals with:
 *   - uuid: a hex string with dashes, in normal UUID order
 *   - binary: a binary string, in optimized order
 *   - hex:  a hex string, no dashes, in optimized order (corresponds to MySQL's
 *           HEX(id) function on a binary format ID).
 *
 * UUID format:
 *
 *  Field                     Type            Octet  Note
 *  -----                     ----            -----  ----
 *  time_low                  unsigned long   0-3    The low field of the timestamp.
 *  time_mid                  unsigned short  4-5    The middle field of the timestamp.
 *  time_hi_and_version       unsigned short  6-7    The high field of the timestamp multiplexed with the version number.
 *  clock_seq_hi_and_reserved unsigned small  8      The high field of the clock sequence multiplexed with the variant.
 *  clock_seq_low             unsigned small  9      The low field of the clock sequence.
 *  node                      character       10-15  The spatially unique node identifier.
 *
 * Returned as a hex string, split by dashes (clock_seq_hi_and_reserved and clock_seq_low combined).
 *
 * When we store this usually, it's as a 36 byte ASCII string (so, we don't deal
 * with collation, stored as "binary", but not using full binary range). But
 * because it's just hex and dashes, we can convert it to a full binary representation
 * of the underlying 16 bytes of information.
 *
 * When we do this, we pack the parts of the UUID in a different order (highest
 * to lowest significance), so that indexing is better and we get cache locality.
 */
class Mysql
{
    const PACK_FORMAT   = 'H12H4H4H4H8';
    const UNPACK_FORMAT = 'H12node/H4clock_seq/H4time_high/H4time_mid/H8time_low';

    /**
     * Whether the given string looks like a hex-encoded UUID
     *
     * @param string $uuid
     * @return boolean
     */
    public static function isUuid($uuid)
    {
        return preg_match('/[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}/i', $uuid);
    }

    /**
     * @param string $uuid 36 characters, hex with dashes
     * @return string 16 byte binary string
     */
    public static function uuidToBinary($uuid)
    {
        list($time_low, $time_mid, $time_high, $clock_seq, $node) = explode('-', $uuid);
        return pack(self::PACK_FORMAT, $node, $clock_seq, $time_high, $time_mid, $time_low);
    }

    /**
     * @param string $uuid 36 character hex, dashes, original UUID order
     * @return string 32 character hex, no dashes, optimized order
     */
    public static function uuidToHex($uuid)
    {
        return self::binaryToHex(self::uuidToBinary($uuid));
    }

    /**
     * @param string $binary 16 bytes
     * @return string 36 characters, hex with dashes
     */
    public static function binaryToUuid($binary)
    {
        $h = unpack(self::UNPACK_FORMAT, $binary);
        return sprintf('%s-%s-%s-%s-%s', $h['time_low'], $h['time_mid'], $h['time_high'], $h['clock_seq'], $h['node']);
    }

    /**
     * @param string $binary 16 bytes
     * @return string 32 character, hex, no dashes
     */
    public static function binaryToHex($binary)
    {
        $h = unpack(self::UNPACK_FORMAT, $binary);
        return implode('', $h);
    }

    /**
     * @param string $hex 32 characters, no dashes, optimized order
     * @return string 16 byte binary
     */
    public static function hexToBinary($hex)
    {
        return pack('H*', $hex);
    }

    /**
     * @param string $hex 32 characters, no dashes, optimized order
     * @return string $uuid 36 characters hex, dashes, original order
     */
    public static function hexToUuid($hex)
    {
        return self::binaryToUuid(self::hexToBinary($hex));
    }
}