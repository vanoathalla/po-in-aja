<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Data Pesanan PO';
    protected static ?string $modelLabel = 'Pesanan';
    protected static ?string $pluralModelLabel = 'Data Pesanan PO';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Informasi Pemesan')
                            ->schema([
                                Forms\Components\TextInput::make('invoice_code')
                                    ->label('Kode Invoice')
                                    ->disabled(),

                                Forms\Components\Select::make('campaign_id')
                                    ->label('Kloter PO')
                                    ->relationship('campaign', 'title')
                                    ->disabled(),

                                Forms\Components\TextInput::make('customer_name')
                                    ->label('Nama Lengkap')
                                    ->required(),

                                Forms\Components\TextInput::make('customer_phone')
                                    ->label('Nomor WhatsApp')
                                    ->tel()
                                    ->required(),

                                Forms\Components\TextInput::make('customer_email')
                                    ->label('Email')
                                    ->email(),

                                Forms\Components\Textarea::make('shipping_address')
                                    ->label('Alamat Pengiriman')
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('notes')
                                    ->label('Catatan Pemesan')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Section::make('Rincian Jersey & Kustomisasi')
                            ->schema([
                                Forms\Components\Repeater::make('items')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\TextInput::make('size')
                                            ->label('Ukuran')
                                            ->disabled(),

                                        Forms\Components\TextInput::make('sleeve_type')
                                            ->label('Lengan')
                                            ->disabled(),

                                        Forms\Components\TextInput::make('custom_name')
                                            ->label('Nama Punggung')
                                            ->disabled(),

                                        Forms\Components\TextInput::make('custom_number')
                                            ->label('No. Punggung')
                                            ->disabled(),

                                        Forms\Components\TextInput::make('quantity')
                                            ->label('Qty')
                                            ->disabled(),

                                        Forms\Components\TextInput::make('total_price')
                                            ->label('Harga')
                                            ->prefix('Rp')
                                            ->disabled(),
                                    ])
                                    ->columns(6)
                                    ->addable(false)
                                    ->deletable(false)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Status & Keuangan')
                            ->schema([
                                Forms\Components\Select::make('payment_status')
                                    ->label('Status Pembayaran')
                                    ->options(PaymentStatus::class)
                                    ->required(),

                                Forms\Components\Select::make('payment_type')
                                    ->label('Skema Pembayaran')
                                    ->options(PaymentType::class)
                                    ->disabled(),

                                Forms\Components\TextInput::make('total_amount')
                                    ->label('Total Tagihan')
                                    ->prefix('Rp')
                                    ->disabled(),

                                Forms\Components\TextInput::make('amount_paid')
                                    ->label('Total Terverifikasi')
                                    ->prefix('Rp')
                                    ->numeric(),

                                Forms\Components\Placeholder::make('remaining_balance_info')
                                    ->label('Sisa Pelunasan')
                                    ->content(fn (?Order $record) => $record ? 'Rp ' . number_format($record->remaining_balance, 0, ',', '.') : '-'),

                                Forms\Components\TextInput::make('rejection_reason')
                                    ->label('Alasan Penolakan (Jika Ada)'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_code')
                    ->label('Invoice')
                    ->searchable()
                    ->copyable()
                    ->weight('bold')
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Pemesan')
                    ->searchable()
                    ->description(fn (Order $record) => $record->customer_phone),

                Tables\Columns\TextColumn::make('campaign.title')
                    ->label('Campaign')
                    ->limit(20)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Status Bayar')
                    ->badge(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Tagihan')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount_paid')
                    ->label('Terbayar')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('remaining_balance')
                    ->label('Sisa Piutang')
                    ->formatStateUsing(fn ($state) => $state > 0 ? 'Rp ' . number_format($state, 0, ',', '.') : 'LUNAS')
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'success')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Order')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('campaign_id')
                    ->label('Filter Campaign')
                    ->relationship('campaign', 'title'),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Status Pembayaran')
                    ->options(PaymentStatus::class),

                Tables\Filters\SelectFilter::make('payment_type')
                    ->label('Tipe Skema Bayar')
                    ->options(PaymentType::class),
            ])
            ->actions([
                // 1. LIHAT BUKTI TRANSFER DARI SUPABASE S3
                Tables\Actions\Action::make('view_proof')
                    ->label('Lihat Bukti')
                    ->icon('heroicon-o-photo')
                    ->color('info')
                    ->modalHeading(fn (Order $record) => "Bukti Transfer Invoice: {$record->invoice_code}")
                    ->modalContent(function (Order $record) {
                        return view('filament.components.order-proof-modal', [
                            'record' => $record,
                        ]);
                    })
                    ->modalSubmitAction(false),

                // 2. TOMBOL APPROVE DP (50%)
                Tables\Actions\Action::make('approve_dp')
                    ->label('Approve DP')
                    ->icon('heroicon-o-check')
                    ->color('warning')
                    ->visible(fn (Order $record) => in_array($record->payment_status, [PaymentStatus::WaitingVerification, PaymentStatus::Unpaid]))
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Verifikasi DP')
                    ->modalDescription(fn (Order $record) => "Verifikasi pembayaran DP sebesar Rp" . number_format($record->down_payment_amount, 0, ',', '.') . " untuk invoice {$record->invoice_code}?")
                    ->form([
                        Forms\Components\TextInput::make('verified_amount')
                            ->label('Nominal Dana Masuk Rekening (Rp)')
                            ->numeric()
                            ->default(fn (Order $record) => $record->down_payment_amount)
                            ->required(),
                    ])
                    ->action(function (Order $record, array $data) {
                        $record->approveDownPayment((float) $data['verified_amount']);

                        Notification::make()
                            ->title('Pembayaran DP Berhasil Diverifikasi')
                            ->success()
                            ->send();
                    }),

                // 3. TOMBOL APPROVE PELUNASAN (LUNAS 100%)
                Tables\Actions\Action::make('approve_final')
                    ->label('Approve Lunas')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (Order $record) => $record->payment_status === PaymentStatus::DpPaid && $record->remaining_balance > 0)
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pelunasan 100%')
                    ->modalDescription(fn (Order $record) => "Pastikan sisa tagihan sebesar Rp" . number_format($record->remaining_balance, 0, ',', '.') . " telah masuk ke rekening panitia.")
                    ->action(function (Order $record) {
                        $record->approveFinalPayment();

                        Notification::make()
                            ->title('Order Telah Lunas Penuh (100%)')
                            ->success()
                            ->send();
                    }),

                // 4. WHATSAPP CHAT BUTTON
                Tables\Actions\Action::make('chat_wa')
                    ->label('WA')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('gray')
                    ->url(function (Order $record) {
                        $phone = preg_replace('/[^0-9]/', '', $record->customer_phone);
                        if (str_starts_with($phone, '0')) {
                            $phone = '62' . substr($phone, 1);
                        }
                        $msg = urlencode("Halo {$record->customer_name}, terima kasih telah memesan di PO {$record->campaign->title}. Status invoice kamu: {$record->invoice_code} adalah {$record->payment_status->getLabel()}. Cek berkala di link: " . url("/track?invoice={$record->invoice_code}"));
                        return "https://wa.me/{$phone}?text={$msg}";
                    }, shouldOpenInNewTab: true),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\OrderResource\Pages\ListOrders::route('/'),
            'create' => \App\Filament\Resources\OrderResource\Pages\CreateOrder::route('/create'),
            'edit' => \App\Filament\Resources\OrderResource\Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
