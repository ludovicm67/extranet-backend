<style>
@page {
  margin: 0;
}

body {
  background: #fff;
  padding: 20px;
  font-family: sans-serif;
}

h1 {
  margin: 0;
  padding: 50px 20px;
  color : #000;
}

small {
  font-size: 18px;
}

table {
  background: #fff;
  width: 100%;
  border-collapse: collapse;
}

thead {
  background: #000;
  color: #fff;
}

table, td {
  border: 1px solid #000;
}

th {
  border: 1px solid #fff;
}

th, td {
  padding: 5px;
}

.row-2 {
  background: #eee;
}
</style>

<h1>
  {{ $name }}
  <small>{{ $period }}</small>
</h1>

<table>
  <thead>
    <tr>
      <th>Nom</th>
      <th>Contrat</th>
      <th>Heures sup</th>
      <th>Congés</th>
      <th>Maladie</th>
      <th>Autres abs.</th>
      <th>Transports</th>
      <th>Dépenses</th>
      <th>Observations</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($lines as $line)
    <tr<?php echo ($loop->iteration % 2 == 0) ? ' class="row-2"' : ''; ?>>
      <td>{{ $line->name }}</td>
      <td>{{ $line->contract }}</td>
      <td style="text-align: right;">
        @if ($line->overtime != 0)
          {{ number_format($line->overtime, 2, ',', ' ') }} h
        @endif
      </td>
      <td style="text-align: right;">
        @if ($line->conges != 0)
          {{ number_format($line->conges, 1, ',', ' ') }}
          jour{{ $line->conges > 1 ? 's' : '' }}
        @endif
      </td>
      <td style="text-align: right;">
        @if ($line->maladie != 0)
          {{ number_format($line->maladie, 1, ',', ' ') }}
          jour{{ $line->maladie > 1 ? 's' : '' }}
        @endif
      </td>
      <td style="text-align: right;">
        @if ($line->autre != 0)
          {{ number_format($line->autre, 1, ',', ' ') }}
          jour{{ $line->autre > 1 ? 's' : '' }}
        @endif
      </td>
      <td style="text-align: right;">
        @if ($line->transports != 0)
          {{ number_format($line->transports, 2, ',', ' ') }} €
        @endif
      </td>
      <td style="text-align: right;">
        @if ($line->expenses != 0)
          {{ number_format($line->expenses, 2, ',', ' ') }} €
        @endif
      </td>
      <td>{!! nl2br(e($line->details)) !!}</td>
    </tr>
    @endforeach
  </tbody>
</table>
