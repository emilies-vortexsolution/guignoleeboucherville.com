<?php
/**
 * Take a date and returns a comprehensive time lapse since that date.
 *
 * @param string $time                   Datetime that can be converted in timestamp.
 * @param array  $extra_attrs            A bundle of extra html attributes to display on `time`
 * @param bool   $is_published_timelapse Will add attributes `pubdate`, `itemprop` and `aria-label` if true.
 *
 * @return string HTML with text representing the timelapse.
 */
function get_html_timelapse( $time, $extra_attrs = array(), $is_published_timelapse = false ) {
  $time           = strtotime( $time );
  $timelapse_text = get_timelapse_text( $time );

  $attrs = array(
    'datetime' => date_i18n( 'Y-m-d H:i', $time ),
  );
  if ( $is_published_timelapse ) {
    $attrs = array(
      'pubdate'    => 'pubdate',
      'itemprop'   => 'datePublished',
      'aria-label' => esc_html_x( 'Published', 'SR Only: Published {date}', 'vtx' ) . " $timelapse_text.",
    );
  }
  $attrs      = array_merge( $attrs, $extra_attrs );
  $html_attrs = convert_array_to_html_attr( $attrs );

  return "<time $html_attrs>$timelapse_text</time>";
}

/**
 * @param int|string|DateTime $time A DateTime directly or a timestamp or a date that can be converted into a DateTime.
 * @param DateTimeZone $timezone
 *
 * @return string
 */
function get_timelapse_text( $time, DateTimeZone $timezone = null ) {
  $timezone = $timezone ?? wp_timezone();

  if ( ! $time instanceof DateTime ) {
    $time = new DateTime( $time, $timezone );
  }

  $actual_datetime = new DateTime( 'now', $timezone );
  $datetime_diff   = $actual_datetime->diff( $time );

  if ( 0 === $datetime_diff->days && 0 === $datetime_diff->h ) {
    $timelapse_text = esc_html_x( 'Less than an hour', 'Timelapse', 'vtx' );

  } elseif ( 0 === $datetime_diff->days ) {
    /* translators: %d: Number of hours */
    $timelapse_text = sprintf( esc_html( _nx( '%d hour ago', '%d hours ago', $datetime_diff->h, 'Timelapse', 'vtx' ) ), $datetime_diff->h );

  } elseif ( 7 > $datetime_diff->days ) {
    $diff = $datetime_diff->days % 7;
    /* translators: %d: Number of days */
    $timelapse_text = sprintf( esc_html( _nx( '%d day ago', '%d days ago', $diff, 'Timelapse', 'vtx' ) ), $diff );

  } elseif ( 0 === $datetime_diff->y ) {
    $diff = $datetime_diff->days / 7 % 52;
    /* translators: %d: Number of weeks */
    $timelapse_text = sprintf( esc_html( _nx( '%d week ago', '%d weeks ago', $diff, 'Timelapse', 'vtx' ) ), $diff );

  } elseif ( 0 < $datetime_diff->y ) {
    /* translators: %d: Number of years */
    $timelapse_text = sprintf( esc_html( _nx( '%d year ago', '%d years ago', $datetime_diff->y, 'Timelapse', 'vtx' ) ), $datetime_diff->y );
  }

  return $timelapse_text;
}

/**
 * Transforms two dates into a readable `From date to date`.
 *
 * @param string $date_start
 * @param string $date_end
 *
 * @return string
 */
function get_readable_from_to_dates( $date_start = '', $date_end = '' ) {
  $formatted_date = '';

  // Only one date means SAME DAY
  if ( empty( $date_end ) ) {
    $formatted_date = date_i18n( 'j F Y', strtotime( $date_start ) );
  } elseif ( ! empty( $date_start ) && ! empty( $date_end ) ) {
    $time_start = strtotime( $date_start );
    $time_end   = strtotime( $date_end );

    $start_day   = date_i18n( 'j', $time_start );
    $end_day     = date_i18n( 'j', $time_end );
    $start_month = date_i18n( 'F', $time_start );
    $end_month   = date_i18n( 'F', $time_end );
    $start_year  = date_i18n( 'Y', $time_start );
    $end_year    = date_i18n( 'Y', $time_end );

    if ( $start_year === $end_year ) {
      // SAME YEAR

      if ( $start_month === $end_month ) {
        // SAME MONTH

        if ( $start_day === $end_day ) {
          // SAME DAY

          $formatted_date = sprintf(
            /* translators: %1$s: day, %2$s: month, %3$s: year */
            esc_html_x( '%1$s %2$s %3$s', 'Formatted date', 'vtx' ),
            $start_day,
            $start_month,
            $start_year
          );
        } else {
          // DIFFERENT DAY

          $formatted_date = sprintf(
            /* translators: %1$s: start day, %2$s: end day, %3$s: month, %4$s: year */
            esc_html_x( 'From %1$s to %2$s %3$s %4$s', 'Formatted date', 'vtx' ),
            $start_day,
            $end_day,
            $start_month,
            $start_year
          );
        }
      } else {
        // DIFFERENT MONTH

        $formatted_date = sprintf(
          /* translators: %1$s: start day, %2$s: start month, %3$s: end day, %4$s: end month, %5$s: year */
          esc_html_x( 'From %1$s %2$s to %3$s %4$s %5$s', 'Formatted date', 'vtx' ),
          $start_day,
          $start_month,
          $end_day,
          $end_month,
          $start_year
        );
      }
    } else {
      // DIFFERENT YEARS

      $formatted_date = sprintf(
        /* translators: %1$s: start day, %2$s: start month, %3$s: start year, %4$s: end day, %5$s: end month, %6$s: end year */
        esc_html_x( 'From %1$s %2$s %3$s to %4$s %5$s %6$s', 'Formatted date', 'vtx' ),
        $start_day,
        $start_month,
        $start_year,
        $end_day,
        $end_month,
        $end_year
      );
    }
  }

  return $formatted_date;
}
