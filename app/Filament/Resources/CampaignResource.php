<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\ProductionStage;
use App\Models\Campaign;
use App\Services\VendorProductionSheetExporter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $navigationLabel = 'Kloter PO Jersey';
    protected static ?string $modelLabel = 'Kloter PO';
    protected static ?string $pluralModelLabel = 'Kloter PO Jersey';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('CampaignTabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Informasi Utama')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Nama Kloter PO')
                                            ->placeholder('Contoh: Jersey Home FTI 2024')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => 
                                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                                            ),

                                        Forms\Components\TextInput::make('slug')
                                            ->label('Slug URL')
                                            ->required()
                                            ->unique(Campaign::class, 'slug', ignoreRecord: true),
                                    ]),

                                Forms\Components\Textarea::make('description')
                                    ->label('Deskripsi PO')
                                    ->rows(3)
                                    ->columnSpanFull(),

                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('base_price')
                                            ->label('Harga Dasar (IDR)')
                                            ->prefix('Rp')
                                            ->numeric()
                                            ->required()
                                            ->default(135000),

                                        Forms\Components\TextInput::make('min_quota')
                                            ->label('Target Minimum Konveksi')
                                            ->suffix('Jersey')
                                            ->numeric()
                                            ->required()
                                            ->default(24),

                                        Forms\Components\TextInput::make('dp_percentage')
                                            ->label('Persentase DP')
                                            ->suffix('%')
                                            ->numeric()
                                            ->required()
                                            ->default(50),
                                    ]),

                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\Select::make('stage')
                                            ->label('Tahapan Produksi')
                                            ->options(ProductionStage::class)
                                            ->required()
                                            ->default(ProductionStage::OpenPo->value),

                                        Forms\Components\DatePicker::make('start_date')
                                            ->label('Mulai Buka PO')
                                            ->required()
                                            ->default(now()),

                                        Forms\Components\DatePicker::make('end_date')
                                            ->label('Batas Akhir PO')
                                            ->required()
                                            ->default(now()->addDays(14)),
                                    ]),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Buka Pendaftaran (Aktif di Web Publik)')
                                    ->default(true),
                            ]),

                        Forms\Components\Tabs\Tab::make('Add-ons & Opsi')
                            ->icon('heroicon-o-puzzle-piece')
                            ->schema([
                                Forms\Components\Repeater::make('addons')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nama Add-on')
                                            ->placeholder('Contoh: Celana 1 Set / Kerah Polo')
                                            ->required(),

                                        Forms\Components\TextInput::make('price')
                                            ->label('Tambahan Biaya (Rp)')
                                            ->prefix('Rp')
                                            ->numeric()
                                            ->required(),

                                        Forms\Components\Select::make('type')
                                            ->label('Kategori')
                                            ->options([
                                                'sleeve' => 'Lengan',
                                                'set' => 'Satu Set Celana',
                                                'collar' => 'Kerah',
                                                'material' => 'Upgrade Bahan Kain',
                                                'general' => 'Umum',
                                            ])
                                            ->default('general'),

                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Aktif')
                                            ->default(true),
                                    ])
                                    ->columns(4)
                                    ->columnSpanFull()
                                    ->addActionLabel('+ Tambah Add-on Baru'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Rekening & Pembayaran')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('bank_name')
                                            ->label('Nama Bank / Dompet')
                                            ->default('BCA')
                                            ->required(),

                                        Forms\Components\TextInput::make('bank_account_number')
                                            ->label('Nomor Rekening')
                                            ->placeholder('Contoh: 8219029188')
                                            ->required(),

                                        Forms\Components\TextInput::make('bank_account_holder')
                                            ->label('Atas Nama Rekening')
                                            ->placeholder('Contoh: Bpk/Ibu Bendahara PO')
                                            ->required(),
                                    ]),

                                Forms\Components\FileUpload::make('qris_image_path')
                                    ->label('Upload Gambar QRIS (Supabase S3)')
                                    ->disk(config('filesystems.default', 's3'))
                                    ->directory('campaign-qris')
                                    ->image()
                                    ->imageEditor()
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Nama Kloter')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('stage')
                    ->label('Tahap Produksi')
                    ->badge(),

                Tables\Columns\TextColumn::make('total_jerseys_ordered')
                    ->label('Terkumpul / Target')
                    ->formatStateUsing(fn ($record) => "{$record->total_jerseys_ordered} / {$record->min_quota} pcs")
                    ->description(fn ($record) => "{$record->quota_progress_percentage}% Kuota Terpenuhi")
                    ->sortable(),

                Tables\Columns\TextColumn::make('base_price')
                    ->label('Harga Dasar')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Tutup PO')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('stage')
                    ->options(ProductionStage::class),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif'),
            ])
            ->actions([
                // 1-CLICK VENDOR EXPORT ACTIONS
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('export_pdf')
                        ->label('Cetak Lembar Konveksi (PDF)')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('danger')
                        ->action(fn (Campaign $record, VendorProductionSheetExporter $exporter) => 
                            $exporter->downloadPdf($record)
                        ),

                    Tables\Actions\Action::make('export_excel')
                        ->label('Export Rekap Matriks (Excel)')
                        ->icon('heroicon-o-table-cells')
                        ->color('success')
                        ->action(fn (Campaign $record, VendorProductionSheetExporter $exporter) => 
                            $exporter->downloadExcel($record)
                        ),
                ])
                ->label('Export Konveksi')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('warning'),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => \App\Filament\Resources\CampaignResource\Pages\ListCampaigns::route('/'),
            'create' => \App\Filament\Resources\CampaignResource\Pages\CreateCampaign::route('/create'),
            'edit' => \App\Filament\Resources\CampaignResource\Pages\EditCampaign::route('/{record}/edit'),
        ];
    }
}
